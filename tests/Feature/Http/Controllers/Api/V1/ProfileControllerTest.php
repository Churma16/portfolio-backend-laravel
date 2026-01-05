<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\V1\ProfileController
 */
final class ProfileControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $profiles = Profile::factory()->count(3)->create();

        $response = $this->get(route('profiles.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\ProfileController::class,
            'store',
            \App\Http\Requests\Api\V1\ProfileStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = fake()->name();

        $response = $this->post(route('profiles.store'), [
            'name' => $name,
        ]);

        $profiles = Profile::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $profiles);
        $profile = $profiles->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $profile = Profile::factory()->create();

        $response = $this->get(route('profiles.show', $profile));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\ProfileController::class,
            'update',
            \App\Http\Requests\Api\V1\ProfileUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $profile = Profile::factory()->create();
        $name = fake()->name();

        $response = $this->put(route('profiles.update', $profile), [
            'name' => $name,
        ]);

        $profile->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $profile->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $profile = Profile::factory()->create();

        $response = $this->delete(route('profiles.destroy', $profile));

        $response->assertNoContent();

        $this->assertModelMissing($profile);
    }
}
