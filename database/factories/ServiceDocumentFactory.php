<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceDocument>
 */
class ServiceDocumentFactory extends Factory
{
    protected $model = ServiceDocument::class;

    public function definition()
    {
        return [
            'service_id' => Service::factory(),
            'document_name' => fake()->words(3, true),
            'document_description' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
