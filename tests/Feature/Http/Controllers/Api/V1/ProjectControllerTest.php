<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\V1\ProjectController
 */
final class ProjectControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $projects = Project::factory()->count(3)->create();

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\ProjectController::class,
            'store',
            \App\Http\Requests\Api\V1\ProjectStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $title = fake()->sentence(4);
        $slug = fake()->slug();
        $is_featured = fake()->boolean();

        $response = $this->post(route('projects.store'), [
            'title' => $title,
            'slug' => $slug,
            'is_featured' => $is_featured,
        ]);

        $projects = Project::query()
            ->where('title', $title)
            ->where('slug', $slug)
            ->where('is_featured', $is_featured)
            ->get();
        $this->assertCount(1, $projects);
        $project = $projects->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\ProjectController::class,
            'update',
            \App\Http\Requests\Api\V1\ProjectUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $project = Project::factory()->create();
        $title = fake()->sentence(4);
        $slug = fake()->slug();
        $is_featured = fake()->boolean();

        $response = $this->put(route('projects.update', $project), [
            'title' => $title,
            'slug' => $slug,
            'is_featured' => $is_featured,
        ]);

        $project->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $project->title);
        $this->assertEquals($slug, $project->slug);
        $this->assertEquals($is_featured, $project->is_featured);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertNoContent();

        $this->assertModelMissing($project);
    }
}
