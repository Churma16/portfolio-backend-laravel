<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReorderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function reorder_endpoint_returns_400_bad_request_when_direction_is_missing(): void
    {
        $adminUser = User::factory()->create();
        Sanctum::actingAs($adminUser, ['admin']);

        $project = Project::factory()->create([
            'column_order' => 1,
        ]);

        $response = $this->postJson("/api/projects/{$project->id}/reorder", []);

        $response->assertStatus(400);
        $response->assertJson([
            'meta' => [
                'code' => 400,
                'status' => 'error',
                'message' => 'Direction is required.',
            ],
            'data' => null,
        ]);
    }
}
