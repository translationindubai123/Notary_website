<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_require_session_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/services')->assertRedirect('/admin/login');
        $this->get('/admin/services/1/preview')->assertRedirect('/admin/login');
        $this->get('/admin/inquiries')->assertRedirect('/admin/login');
        $this->post('/admin/services')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Sign in');
        $this->get('/register')->assertNotFound();
    }

    public function test_authenticated_admin_can_search_and_read_contact_inquiries_safely(): void
    {
        $inquiry = ContactInquiry::create([
            'name' => 'Inquiry Sender',
            'email' => 'sender@example.test',
            'subject' => 'Service question',
            'message' => "<script>alert('xss')</script>\nPlease reply.",
        ]);
        $this->actingAs(User::factory()->create());

        $this->get('/admin/inquiries?q=sender')
            ->assertOk()
            ->assertSee('Inquiry Sender')
            ->assertSee('sender@example.test');
        $this->get('/admin/inquiries/'.$inquiry->id)
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;', false)
            ->assertDontSee("<script>alert('xss')</script>", false)
            ->assertSee('mailto:sender@example.test', false);
    }

    public function test_administrator_can_sign_in_and_sign_out_using_a_session(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('a-strong-test-password'),
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@example.test',
            'password' => 'a-strong-test-password',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_manage_categories_and_services_with_repeatable_content(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);
        $this->get('/admin/services/create')->assertOk()->assertSee('service_type" value="notary"', false);
        $this->post('/admin/categories', [
            'name' => 'Smart Document Request',
            'slug' => 'smart-document-request',
            'sort_order' => 0,
            'is_active' => '1',
        ])->assertRedirect();
        $category = ServiceCategory::whereSlug('smart-document-request')->firstOrFail();

        $this->post('/admin/services', [
            'service_category_id' => $category->id,
            'title' => 'Power of Attorney',
            'slug' => 'power-of-attorney',
            'short_description' => 'Description',
            'sort_order' => 0,
            'is_active' => '1',
            'documents' => [['document_name' => 'Sample document', 'document_description' => 'For testing']],
            'process_steps' => [['step_title' => 'Sample step', 'step_description' => 'For testing']],
            'faqs' => [['question' => 'Sample question?', 'answer' => 'Sample answer.']],
        ])->assertRedirect();

        $service = Service::whereSlug('power-of-attorney')->firstOrFail();
        $this->assertTrue($service->is_active);
        $this->assertNotNull($service->published_at);
        $this->assertDatabaseHas('service_documents', ['service_id' => $service->id, 'document_name' => 'Sample document']);
        $this->assertDatabaseHas('service_process_steps', ['service_id' => $service->id, 'step_title' => 'Sample step']);
        $this->assertDatabaseHas('service_faqs', ['service_id' => $service->id, 'question' => 'Sample question?']);
        $this->get('/admin/services/'.$service->id.'/edit')->assertOk()->assertSee('Power of Attorney');
        $this->get('/admin/services/'.$service->id.'/preview')
            ->assertOk()
            ->assertSee('Admin preview')
            ->assertSee('this service is publicly published')
            ->assertSee($service->publicUrl(), false);

        $this->put('/admin/services/'.$service->id, [
            'service_category_id' => $category->id,
            'title' => 'Power of Attorney',
            'slug' => 'power-of-attorney',
            'sort_order' => 0,
            'is_active' => '',
            'documents' => [['document_name' => '', 'document_description' => '']],
            'process_steps' => [['step_title' => '', 'step_description' => '']],
            'faqs' => [['question' => '', 'answer' => '']],
        ])->assertRedirect();

        $this->assertFalse($service->fresh()->is_active);
        $this->get('/admin/services/'.$service->id.'/preview')
            ->assertOk()
            ->assertSee('this content is not publicly published');
        $this->get('/notary-services/service/power-of-attorney')->assertNotFound();
    }

    public function test_duplicate_category_slug_is_rejected_and_categories_with_services_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $category = ServiceCategory::create(['name' => 'One', 'slug' => 'one']);
        $this->actingAs($admin);

        $this->post('/admin/categories', [
            'name' => 'Duplicate',
            'slug' => 'one',
            'sort_order' => 0,
        ])->assertSessionHasErrors('slug');

        $service = Service::create(['service_category_id' => $category->id, 'title' => 'An item', 'slug' => 'an-item']);
        $this->delete('/admin/categories/'.$category->id)->assertSessionHasErrors('category');
        $this->assertDatabaseHas('service_categories', ['id' => $category->id]);

        $this->post('/admin/services', [
            'service_category_id' => $category->id,
            'title' => 'Duplicate item',
            'slug' => 'an-item',
            'sort_order' => 0,
        ])->assertSessionHasErrors('slug');

        $this->delete('/admin/services/'.$service->id)->assertRedirect();
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
        $this->delete('/admin/categories/'.$category->id)->assertRedirect();
        $this->assertDatabaseMissing('service_categories', ['id' => $category->id]);
    }

    public function test_category_can_be_updated(): void
    {
        $category = ServiceCategory::create(['name' => 'Old name', 'slug' => 'old-name']);
        $this->actingAs(User::factory()->create());

        $this->put('/admin/categories/'.$category->id, [
            'name' => 'Updated name',
            'slug' => 'updated-name',
            'sort_order' => 5,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('service_categories', [
            'id' => $category->id,
            'name' => 'Updated name',
            'slug' => 'updated-name',
            'sort_order' => 5,
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_manage_moj_categories_and_services_separately(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/services/create?type=moj')->assertOk()->assertSee('service_type" value="moj"', false);
        $this->post('/admin/categories?type=moj', [
            'service_type' => 'moj',
            'name' => 'MOJ Documents',
            'slug' => 'moj-documents',
            'sort_order' => 0,
            'is_active' => '1',
        ])->assertRedirect();
        $category = ServiceCategory::whereSlug('moj-documents')->firstOrFail();
        $this->assertSame('moj', $category->service_type);

        $this->post('/admin/services?type=moj', [
            'service_type' => 'moj',
            'service_category_id' => $category->id,
            'title' => 'MOJ Certified Copy',
            'slug' => 'moj-certified-copy',
            'sort_order' => 0,
            'is_active' => '1',
        ])->assertRedirect();

        $this->get('/admin/categories?type=moj')->assertOk()->assertSee('MOJ Documents');
        $this->get('/admin/categories?type=notary')->assertOk()->assertDontSee('MOJ Documents');
        $this->get('/admin/services?type=moj')->assertOk()->assertSee('MOJ Certified Copy');
        $this->get('/admin/services?type=notary')->assertOk()->assertDontSee('MOJ Certified Copy');
        $this->get('/moj-services/services')->assertOk()->assertSee('MOJ Certified Copy');
    }

    public function test_service_image_upload_and_replacement_use_the_public_disk(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $category = ServiceCategory::create([
            'name' => 'Image category',
            'slug' => 'image-category',
            'is_active' => true,
        ]);

        $this->post('/admin/services', [
            'service_category_id' => $category->id,
            'title' => 'Image service',
            'slug' => 'image-service',
            'sort_order' => 0,
            'is_active' => '1',
            'featured_image' => UploadedFile::fake()->createWithContent(
                'first-image.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
            ),
            'image_alt' => 'First service image',
        ])->assertRedirect();

        $service = Service::whereSlug('image-service')->firstOrFail();
        $oldImage = $service->featured_image;
        Storage::disk('public')->assertExists($oldImage);
        $this->get('/notary-services/'.$category->slug.'/image-service')
            ->assertOk()
            ->assertSee(asset('storage/'.$oldImage), false);

        $this->put('/admin/services/'.$service->id, [
            'service_category_id' => $category->id,
            'title' => 'Image service',
            'slug' => 'image-service',
            'sort_order' => 0,
            'is_active' => '1',
            'featured_image' => UploadedFile::fake()->createWithContent(
                'replacement-image.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
            ),
            'image_alt' => 'Replacement service image',
        ])->assertRedirect();

        $newImage = $service->fresh()->featured_image;
        $this->assertNotSame($oldImage, $newImage);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($newImage);
        $this->get('/admin/services/'.$service->id.'/edit')
            ->assertOk()
            ->assertSee(asset('storage/'.$newImage), false);
    }

    public function test_site_settings_update_business_contact_and_managed_home_and_moj_content(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');
        SiteSetting::create(['setting_key' => 'moj_title', 'setting_value' => 'Existing MOJ title']);
        SiteSetting::create(['setting_key' => 'moj_introduction', 'setting_value' => 'Existing MOJ introduction']);
        SiteSetting::create(['setting_key' => 'moj_body', 'setting_value' => 'Existing MOJ body']);
        $this->put('/admin/settings', [
            'site_name' => 'Example Legal Services',
            'site_description' => 'An approved business description.',
            'home_hero_eyebrow' => 'DOCUMENT SERVICES · DUBAI',
            'home_hero_title' => 'Notary services for documents',
            'home_hero_introduction' => 'A clear description of document information.',
            'home_meta_description' => 'Explore document notary services and contact our team for guidance.',
            'home_primary_cta' => 'Explore services',
            'home_secondary_cta' => 'Contact our team',
            'site_phone' => '+971 50 123 4567',
            'site_whatsapp' => '971501234567',
            'site_email' => 'hello@example.test',
            'site_inquiries_email' => 'inquiries@example.test',
            'site_address' => 'Verified business address.',
            'site_office_hours' => 'Monday to Friday',
            'site_logo' => UploadedFile::fake()->createWithContent(
                'site-logo.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
            ),
        ])->assertRedirect('/admin/settings');

        $logoPath = SiteSetting::value('site_logo');
        Storage::disk('public')->assertExists($logoPath);
        $this->get('/about-us')->assertOk()->assertSee('Making Every Document Journey Simpler');
        $this->get('/admin/settings')
            ->assertOk()
            ->assertDontSee('MOJ Services page')
            ->assertDontSee('name="moj_title"', false)
            ->assertDontSee('name="moj_introduction"', false)
            ->assertDontSee('name="moj_body"', false);
        $this->get('/')
            ->assertOk()
            ->assertSee('Notary services for documents')
            ->assertSee('DOCUMENT SERVICES · DUBAI')
            ->assertSee('Explore services');
        $this->get('/moj-services')->assertOk()->assertSee('Existing MOJ introduction');
        $this->get('/contact')
            ->assertOk()
            ->assertSee('hello@example.test')
            ->assertSee('971501234567')
            ->assertSee('contact-map-frame', false)
            ->assertSee(rawurlencode('Verified business address.'), false)
            ->assertSee('"@type":"ContactPage"', false)
            ->assertSee('"hasMap"', false)
            ->assertSee('"streetAddress":"Verified business address."', false)
            ->assertSee('maps.google.com/maps?q='.rawurlencode('Verified business address.'), false)
            ->assertSee(asset('storage/'.$logoPath), false)
            ->assertSee('class="brand-logo"', false);
        $this->get('/admin/settings')->assertOk()->assertSee(asset('storage/'.$logoPath), false);
        $this->assertDatabaseHas('site_settings', ['setting_key' => 'site_name', 'setting_value' => 'Example Legal Services']);
        $this->assertDatabaseHas('site_settings', ['setting_key' => 'moj_title', 'setting_value' => 'Existing MOJ title']);
        $this->assertDatabaseHas('site_settings', ['setting_key' => 'moj_introduction', 'setting_value' => 'Existing MOJ introduction']);
        $this->assertDatabaseHas('site_settings', ['setting_key' => 'moj_body', 'setting_value' => 'Existing MOJ body']);
    }
}
