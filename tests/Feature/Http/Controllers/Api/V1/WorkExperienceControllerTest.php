<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\WorkExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\V1\WorkExperienceController
 */
final class WorkExperienceControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $workExperiences = WorkExperience::factory()->count(3)->create();

        $response = $this->get(route('work-experiences.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\WorkExperienceController::class,
            'store',
            \App\Http\Requests\Api\V1\WorkExperienceStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $company = fake()->company();
        $position = fake()->word();
        $start_date = Carbon::parse(fake()->date());
        $is_current = fake()->boolean();

        $response = $this->post(route('work-experiences.store'), [
            'company' => $company,
            'position' => $position,
            'start_date' => $start_date->toDateString(),
            'is_current' => $is_current,
        ]);

        $workExperiences = WorkExperience::query()
            ->where('company', $company)
            ->where('position', $position)
            ->where('start_date', $start_date)
            ->where('is_current', $is_current)
            ->get();
        $this->assertCount(1, $workExperiences);
        $workExperience = $workExperiences->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $workExperience = WorkExperience::factory()->create();

        $response = $this->get(route('work-experiences.show', $workExperience));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\V1\WorkExperienceController::class,
            'update',
            \App\Http\Requests\Api\V1\WorkExperienceUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $workExperience = WorkExperience::factory()->create();
        $company = fake()->company();
        $position = fake()->word();
        $start_date = Carbon::parse(fake()->date());
        $is_current = fake()->boolean();

        $response = $this->put(route('work-experiences.update', $workExperience), [
            'company' => $company,
            'position' => $position,
            'start_date' => $start_date->toDateString(),
            'is_current' => $is_current,
        ]);

        $workExperience->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($company, $workExperience->company);
        $this->assertEquals($position, $workExperience->position);
        $this->assertEquals($start_date, $workExperience->start_date);
        $this->assertEquals($is_current, $workExperience->is_current);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $workExperience = WorkExperience::factory()->create();

        $response = $this->delete(route('work-experiences.destroy', $workExperience));

        $response->assertNoContent();

        $this->assertModelMissing($workExperience);
    }
}
