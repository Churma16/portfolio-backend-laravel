<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\V1\TagController
 */
final class TagControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $tags = Tag::factory()->count(3)->create();

        $response = $this->get(route('tags.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\TagController::class,
            'store',
            \App\Http\Requests\Api\V1\TagStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = fake()->name();
        $slug = fake()->slug();

        $response = $this->post(route('tags.store'), [
            'name' => $name,
            'slug' => $slug,
        ]);

        $tags = Tag::query()
            ->where('name', $name)
            ->where('slug', $slug)
            ->get();
        $this->assertCount(1, $tags);
        $tag = $tags->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get(route('tags.show', $tag));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\TagController::class,
            'update',
            \App\Http\Requests\Api\V1\TagUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $tag = Tag::factory()->create();
        $name = fake()->name();
        $slug = fake()->slug();

        $response = $this->put(route('tags.update', $tag), [
            'name' => $name,
            'slug' => $slug,
        ]);

        $tag->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $tag->name);
        $this->assertEquals($slug, $tag->slug);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->delete(route('tags.destroy', $tag));

        $response->assertNoContent();

        $this->assertModelMissing($tag);
    }
}
