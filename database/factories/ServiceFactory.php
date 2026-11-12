<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        $title = fake()->unique()->words(3, true);

        return [
            'service_category_id' => ServiceCategory::factory(),
            'title' => Str::title($title),
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'short_description' => fake()->optional()->sentence(),
            'introduction' => fake()->optional()->paragraph(),
            'description' => fake()->optional()->paragraphs(3, true),
            'eligibility' => fake()->optional()->paragraph(),
            'processing_time' => fake()->optional()->randomElement(['1 business day', '2-3 business days']),
            'service_availability' => fake()->optional()->randomElement(['By appointment', 'Online']),
            'important_notes' => fake()->optional()->sentence(),
            'who_needs_service' => fake()->optional()->paragraph(),
            'meta_title' => fake()->optional()->sentence(5),
            'meta_description' => fake()->optional()->text(150),
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'featured_image' => null,
            'image_alt' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'is_active' => false,
            'published_at' => null,
        ];
    }

    public function published()
    {
        return $this->state(fn () => [
            'service_category_id' => ServiceCategory::factory()->active(),
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);
    }

    public function scheduled()
    {
        return $this->state(fn () => [
            'service_category_id' => ServiceCategory::factory()->active(),
            'is_active' => true,
            'published_at' => now()->addDay(),
        ]);
    }

    public function featured()
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
