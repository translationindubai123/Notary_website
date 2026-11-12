<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_validates_and_saves_a_submission(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Power of Attorney',
            'slug' => 'power-of-attorney',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->post('/contact', [
            'name' => 'Test Sender',
            'email' => 'not-an-email',
            'phone' => '+971500000000',
            'subject' => 'Service inquiry',
            'message' => 'Please contact me about a service.',
        ])->assertSessionHasErrors('email');

        $this->post('/contact', [
            '_token' => csrf_token(),
            'name' => 'Test Sender',
            'email' => 'sender@example.test',
            'phone' => '+971500000000',
            'service_id' => $service->id,
            'subject' => 'Service inquiry',
            'message' => 'Please contact me about a service.',
        ])->assertRedirect('/contact')->assertSessionHas('status');

        $this->assertDatabaseHas('contact_inquiries', [
            'name' => 'Test Sender',
            'email' => 'sender@example.test',
            'subject' => 'Service inquiry',
            'service_id' => $service->id,
        ]);
    }

    public function test_contact_form_offers_published_services_and_rejects_unavailable_selection(): void
    {
        $category = ServiceCategory::create(['name' => 'Requests', 'slug' => 'requests', 'is_active' => true]);
        $published = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Published service',
            'slug' => 'published-service',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
        $draft = Service::create([
            'service_category_id' => $category->id,
            'title' => 'Draft service',
            'slug' => 'draft-service',
            'is_active' => false,
        ]);

        $this->get('/contact?service=published-service')
            ->assertOk()
            ->assertSee('Published service')
            ->assertDontSee('Draft service')
            ->assertSee('value="'.$published->id.'" selected', false);
        $this->post('/contact', [
            'name' => 'Test Sender',
            'email' => 'sender@example.test',
            'message' => 'Please contact me.',
            'service_id' => $draft->id,
        ])->assertSessionHasErrors('service_id');
        $this->assertDatabaseMissing('contact_inquiries', ['email' => 'sender@example.test']);
    }

    public function test_contact_honeypot_rejects_filled_values(): void
    {
        $this->post('/contact', [
            'name' => 'Test Sender',
            'email' => 'sender@example.test',
            'message' => 'Message.',
            'company_website' => 'https://spam.example',
        ])->assertSessionHasErrors('company_website');
    }

    public function test_contact_subject_rejects_header_line_breaks(): void
    {
        $this->post('/contact', [
            'name' => 'Test Sender',
            'email' => 'sender@example.test',
            'subject' => "Inquiry\r\nBcc: attacker@example.test",
            'message' => 'Message.',
        ])->assertSessionHasErrors('subject');
    }

    public function test_inquiry_is_saved_and_notification_failure_is_shown_to_the_sender(): void
    {
        SiteSetting::create([
            'setting_key' => 'site_inquiries_email',
            'setting_value' => 'inquiries@example.test',
        ]);
        config(['mail.default' => 'invalid-mailer']);
        Log::spy();

        $this->post('/contact', [
            'name' => 'Test Sender',
            'email' => 'sender@example.test',
            'message' => 'Please contact me.',
        ])->assertRedirect('/contact')->assertSessionHas('status')
            ->assertSessionHas('notification_warning');

        $this->assertDatabaseHas('contact_inquiries', ['email' => 'sender@example.test']);
        Log::shouldHaveReceived('error')->once();
    }
}
