<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    protected $model = SiteSetting::class;

    public function definition()
    {
        return [
            'setting_key' => Str::slug(fake()->unique()->words(2, true), '_'),
            'setting_value' => fake()->sentence(),
        ];
    }
}
