<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceDocument;
use App\Models\ServiceFaq;
use App\Models\ServiceProcessStep;
use App\Models\SiteSetting;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DatabasePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_factories_create_records_and_eloquent_relationships_round_trip(): void
    {
        $parent = ServiceCategory::factory()->active()->create();
        $child = ServiceCategory::factory()->active()->create(['parent_id' => $parent->id]);
        $service = Service::factory()->published()->create(['service_category_id' => $child->id]);
        $relatedService = Service::factory()->published()->create(['service_category_id' => $child->id]);
        $document = ServiceDocument::factory()->create(['service_id' => $service->id]);
        $step = ServiceProcessStep::factory()->create(['service_id' => $service->id]);
        $faq = ServiceFaq::factory()->create(['service_id' => $service->id]);
        $inquiry = ContactInquiry::factory()->create();
        $setting = SiteSetting::factory()->create();

        $service->relatedServices()->attach($relatedService);

        $this->assertTrue($child->parent->is($parent));
        $this->assertTrue($parent->children->first()->is($child));
        $this->assertTrue($service->category->is($child));
        $this->assertTrue($child->services->contains($service));
        $this->assertTrue($service->documents->contains($document));
        $this->assertTrue($service->processSteps->contains($step));
        $this->assertTrue($service->faqs->contains($faq));
        $this->assertTrue($service->relatedServices->contains($relatedService));
        $this->assertTrue($relatedService->relatedFromServices->contains($service));
        $this->assertSame($inquiry->email, ContactInquiry::findOrFail($inquiry->id)->email);
        $this->assertSame($setting->setting_value, SiteSetting::findOrFail($setting->id)->setting_value);
        $this->assertSame($setting->setting_value, SiteSetting::value($setting->setting_key));
        $this->assertTrue(Service::published()->whereKey($service->id)->exists());
    }

    public function test_published_factory_creates_an_active_category_and_scheduled_services_are_not_published(): void
    {
        $published = Service::factory()->published()->create();
        $scheduled = Service::factory()->scheduled()->create();

        $this->assertTrue($published->category->is_active);
        $this->assertTrue(Service::published()->whereKey($published->id)->exists());
        $this->assertTrue($scheduled->category->is_active);
        $this->assertFalse(Service::published()->whereKey($scheduled->id)->exists());
    }

    public function test_service_deletion_cascades_repeatable_content_and_related_pivot_rows(): void
    {
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create(['service_category_id' => $category->id]);
        $related = Service::factory()->create(['service_category_id' => $category->id]);
        $service->documents()->create(['document_name' => 'Demo document']);
        $service->processSteps()->create(['step_title' => 'Demo step', 'step_description' => 'Demo description']);
        $service->faqs()->create(['question' => 'Demo question?', 'answer' => 'Demo answer.']);
        $service->relatedServices()->attach($related);

        $service->delete();

        $this->assertDatabaseMissing('service_documents', ['service_id' => $service->id]);
        $this->assertDatabaseMissing('service_process_steps', ['service_id' => $service->id]);
        $this->assertDatabaseMissing('service_faqs', ['service_id' => $service->id]);
        $this->assertDatabaseMissing('service_related_services', ['service_id' => $service->id]);
    }

    public function test_database_rejects_duplicate_slugs_and_prevents_deleting_categories_with_services(): void
    {
        $category = ServiceCategory::factory()->create(['slug' => 'unique-category']);
        ServiceCategory::factory()->create(['slug' => 'other-category']);
        $service = Service::factory()->create([
            'service_category_id' => $category->id,
            'slug' => 'unique-service',
        ]);

        try {
            Service::factory()->create([
                'service_category_id' => $category->id,
                'slug' => 'unique-service',
            ]);
            $this->fail('The unique service slug constraint was not enforced.');
        } catch (QueryException $exception) {
            $this->assertNotEmpty($exception->getMessage());
        }

        try {
            $category->delete();
            $this->fail('The category foreign-key restriction was not enforced.');
        } catch (QueryException $exception) {
            $this->assertNotEmpty($exception->getMessage());
        }

        $this->assertDatabaseHas('services', ['id' => $service->id]);
        $this->assertDatabaseHas('service_categories', ['id' => $category->id]);
    }

    public function test_demo_seeder_is_idempotent_and_keeps_demo_services_unpublished(): void
    {
        Artisan::call('db:seed', ['--class' => DemoContentSeeder::class]);
        Artisan::call('db:seed', ['--class' => DemoContentSeeder::class]);

        $this->assertDatabaseCount('service_categories', 4);
        $this->assertDatabaseCount('services', 2);
        $this->assertDatabaseCount('service_documents', 1);
        $this->assertDatabaseCount('service_process_steps', 1);
        $this->assertDatabaseCount('service_faqs', 1);
        $this->assertDatabaseCount('service_related_services', 1);
        $this->assertDatabaseHas('services', [
            'slug' => 'sample-service-record-one',
            'is_active' => 0,
            'published_at' => null,
        ]);
        $this->assertDatabaseHas('services', [
            'slug' => 'sample-service-record-two',
            'is_active' => 0,
            'published_at' => null,
        ]);
    }
}
