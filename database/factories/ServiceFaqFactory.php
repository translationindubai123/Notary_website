<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceFaq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceFaq>
 */
class ServiceFaqFactory extends Factory
{
    protected $model = ServiceFaq::class;

    public function definition()
    {
        return [
            'service_id' => Service::factory(),
            'question' => fake()->sentence(),
            'answer' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
