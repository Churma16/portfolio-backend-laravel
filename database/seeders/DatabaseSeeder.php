<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use App\Models\Project;
use App\Models\TechStack;
use App\Models\WorkExperience;
use Database\Seeders\TagSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\ProfileSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TechStackSeeder;
use Database\Seeders\WorkExperienceSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the basic data using your existing seeders
        $this->call([
            ProfileSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            TechStackSeeder::class,
            ProjectSeeder::class,
            WorkExperienceSeeder::class,
        ]);

        // 2. Fetch the newly created data from the database
        $tags = Tag::all();
        $techStacks = TechStack::all();
        $projects = Project::all();
        $workExperiences = WorkExperience::all();

        // 3. Link Projects to Tags and TechStacks (Pivot Tables)
        $projects->each(function ($project) use ($tags, $techStacks) {
            $project->tags()->sync($tags->random(rand(2, 4))->pluck('id'));
            $project->techStacks()->sync($techStacks->random(rand(3, 5))->pluck('id'));
        });

        // 4. Link WorkExperience to Tags and TechStacks (Pivot Tables)
        $workExperiences->each(function ($work) use ($tags, $techStacks) {
            $work->tags()->sync($tags->random(rand(1, 3))->pluck('id'));
            $work->techStacks()->sync($techStacks->random(rand(2, 4))->pluck('id'));
        });

        // 5. Create the test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
