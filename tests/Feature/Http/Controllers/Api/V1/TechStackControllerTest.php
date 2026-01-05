<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\TechStack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\V1\TechStackController
 */
final class TechStackControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $techStacks = TechStack::factory()->count(3)->create();

        $response = $this->get(route('tech-stacks.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\TechStackController::class,
            'store',
            \App\Http\Requests\Api\V1\TechStackStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = fake()->name();
        $slug = fake()->slug();

        $response = $this->post(route('tech-stacks.store'), [
            'name' => $name,
            'slug' => $slug,
        ]);

        $techStacks = TechStack::query()
            ->where('name', $name)
            ->where('slug', $slug)
            ->get();
        $this->assertCount(1, $techStacks);
        $techStack = $techStacks->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $techStack = TechStack::factory()->create();

        $response = $this->get(route('tech-stacks.show', $techStack));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\TechStackController::class,
            'update',
            \App\Http\Requests\Api\V1\TechStackUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $techStack = TechStack::factory()->create();
        $name = fake()->name();
        $slug = fake()->slug();

        $response = $this->put(route('tech-stacks.update', $techStack), [
            'name' => $name,
            'slug' => $slug,
        ]);

        $techStack->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $techStack->name);
        $this->assertEquals($slug, $techStack->slug);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $techStack = TechStack::factory()->create();

        $response = $this->delete(route('tech-stacks.destroy', $techStack));

        $response->assertNoContent();

        $this->assertModelMissing($techStack);
    }
}
