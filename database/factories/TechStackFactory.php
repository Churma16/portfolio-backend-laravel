<?php

namespace Database\Factories;

use App\Models\Category;
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
            'category_id' => Category::factory(),
        ];
    }
}
