<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoContentSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $categoryDefinitions = [
                ['name' => 'Smart Document Request', 'slug' => 'smart-document-request'],
                ['name' => 'Writ of Execution', 'slug' => 'writ-of-execution'],
                ['name' => 'Notification by Publication', 'slug' => 'notification-by-publication'],
                ['name' => 'Smart Notary Error Correction', 'slug' => 'smart-notary-error-correction'],
            ];

            foreach ($categoryDefinitions as $attributes) {
                ServiceCategory::firstOrCreate(
                    ['slug' => $attributes['slug']],
                    ['name' => $attributes['name'], 'is_active' => true]
                );
            }

            $category = ServiceCategory::where('slug', 'smart-document-request')->firstOrFail();
            $services = [
                [
                    'slug' => 'sample-service-record-one',
                    'title' => 'Sample service record one (demo)',
                    'short_description' => 'Demonstration placeholder. Replace with approved service information.',
                ],
                [
                    'slug' => 'sample-service-record-two',
                    'title' => 'Sample service record two (demo)',
                    'short_description' => 'Demonstration placeholder. Replace with approved service information.',
                ],
            ];

            foreach ($services as $attributes) {
                Service::firstOrCreate(
                    ['slug' => $attributes['slug']],
                    [
                        'service_category_id' => $category->id,
                        'title' => $attributes['title'],
                        'short_description' => $attributes['short_description'],
                        'introduction' => 'DEMO CONTENT — unpublished placeholder for testing the content model.',
                        'description' => 'This sample is not legal advice and does not describe verified service requirements.',
                        'is_active' => false,
                        'is_featured' => false,
                    ]
                );
            }

            $first = Service::where('slug', 'sample-service-record-one')->firstOrFail();
            $second = Service::where('slug', 'sample-service-record-two')->firstOrFail();

            if (! $first->documents()->exists()) {
                $first->documents()->create([
                    'document_name' => 'Sample document (demo)',
                    'document_description' => 'Placeholder only; confirm all requirements before publishing.',
                    'sort_order' => 0,
                ]);
            }
            if (! $first->processSteps()->exists()) {
                $first->processSteps()->create([
                    'step_title' => 'Sample process step (demo)',
                    'step_description' => 'Placeholder instruction for testing only.',
                    'sort_order' => 0,
                ]);
            }
            if (! $first->faqs()->exists()) {
                $first->faqs()->create([
                    'question' => 'Sample question (demo)?',
                    'answer' => 'Placeholder answer for testing only.',
                    'sort_order' => 0,
                ]);
            }

            $first->relatedServices()->syncWithoutDetaching([$second->id]);
        });
    }
}
