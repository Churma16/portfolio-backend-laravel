<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'headline' => fake()->regexify('[A-Za-z0-9]{255}'),
            'bio_short' => fake()->text(),
            'bio_short' => fake()->text(),
            'avatar' => fake()->regexify('[A-Za-z0-9]{500}'),
            'cv_files' => fake()->regexify('[A-Za-z0-9]{500}'),
            'socials' => [
                'github' => 'https://github.com/' . $this->faker->userName,
                'linkedin' => 'https://linkedin.com/in/' . $this->faker->userName,
                'instagram' => 'https://instagram.com/' . $this->faker->userName,
            ],
        ];
    }
}
