<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_visitors_can_submit_contact_messages(): void
    {
        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $contactPayload = [
            'name' => 'Fathan Test',
            'email' => 'visitor@example.com',
            'content' => 'Hello from public contact form automated test.',
        ];

        $response = $this->postJson('/api/messages', $contactPayload);

        $response->assertOk();
        $this->assertDatabaseHas('messages', [
            'name' => 'Fathan Test',
            'email' => 'visitor@example.com',
        ]);

        Http::assertSent(function ($request) {
            return $request['name'] === 'Fathan Test' &&
                   $request['email'] === 'visitor@example.com' &&
                   $request['message'] === 'Hello from public contact form automated test.' &&
                   $request['source'] === 'laravel';
        });
    }

    #[Test]
    public function contact_message_submission_validates_required_fields(): void
    {
        Http::fake();

        $response = $this->postJson('/api/messages', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'content']);

        Http::assertNothingSent();
    }

    #[Test]
    public function admin_message_listing_requires_authentication(): void
    {
        $response = $this->getJson('/api/messages');

        $response->assertStatus(401);
    }
}
