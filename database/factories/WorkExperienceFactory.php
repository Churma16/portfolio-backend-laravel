<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'position' => fake()->regexify('[A-Za-z0-9]{200}'),
            'location' => fake()->regexify('[A-Za-z0-9]{200}'),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'is_current' => fake()->boolean(),
            'description' => fake()->text(),
        ];
    }
}
