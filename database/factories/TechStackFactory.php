<?php

namespace Database\Factories;

use App\Models\TechStackCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class TechStackFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'icon' => fake()->regexify('[A-Za-z0-9]{255}'),
            'tech_stack_category_id' => TechStackCategory::factory(),
        ];
    }
}
