<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    protected $model = ServiceCategory::class;

    public function definition()
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'short_description' => fake()->optional()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'meta_title' => fake()->optional()->sentence(5),
            'meta_description' => fake()->optional()->text(150),
            'canonical_url' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => false,
        ];
    }

    public function active()
    {
        return $this->state(fn () => ['is_active' => true]);
    }
}
