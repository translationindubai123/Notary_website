<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceProcessStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceProcessStep>
 */
class ServiceProcessStepFactory extends Factory
{
    protected $model = ServiceProcessStep::class;

    public function definition()
    {
        return [
            'service_id' => Service::factory(),
            'step_title' => fake()->sentence(4),
            'step_description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
