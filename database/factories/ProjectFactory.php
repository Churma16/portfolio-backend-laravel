<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->slug(),
            'thumbnail' => fake()->regexify('[A-Za-z0-9]{500}'),
            'content' => fake()->paragraphs(3, true),
            'demo_url' => fake()->regexify('[A-Za-z0-9]{500}'),
            'repo_url' => fake()->regexify('[A-Za-z0-9]{500}'),
            'is_featured' => fake()->boolean(),
            'published_at' => fake()->dateTime(),
        ];
    }
}
