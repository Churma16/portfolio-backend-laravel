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

    #[Test]
    public function contact_submission_emits_telemetry_when_monitor_is_configured(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        \Illuminate\Support\Facades\Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $payload = [
            'name' => 'Telemetry Visitor',
            'email' => 'telemetry@example.com',
            'content' => 'Testing telemetry event dispatching.',
        ];

        $response = $this->postJson('/api/messages', $payload);

        $response->assertOk();

        // 1. Check contact_submit event sent to monitor
        Http::assertSent(function ($request) use ($monitorUrl) {
            return $request->url() === $monitorUrl &&
                   $request['source'] === 'laravel' &&
                   $request['type'] === 'contact_submit';
        });

        // 2. Check http_request event sent to monitor on terminate
        Http::assertSent(function ($request) use ($monitorUrl) {
            return $request->url() === $monitorUrl &&
                   $request['source'] === 'laravel' &&
                   $request['type'] === 'http_request' &&
                   $request['status_code'] === 200;
        });
    }

    #[Test]
    public function honeypot_submission_emits_spam_blocked_without_pii(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        \Illuminate\Support\Facades\Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $spamPayload = [
            'name' => 'Spam Bot',
            'email' => 'spambot@example.com',
            'content' => 'Spam content advertisement',
            'gotcha' => 'i_am_a_bot',
        ];

        $response = $this->postJson('/api/messages', $spamPayload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Pesan terkirim!',
        ]);

        // Assert message not stored in database
        $this->assertDatabaseMissing('messages', [
            'email' => 'spambot@example.com',
        ]);

        // Assert spam_blocked event was sent to monitor
        Http::assertSent(function ($request) use ($monitorUrl) {
            if ($request->url() !== $monitorUrl || $request['type'] !== 'spam_blocked') {
                return false;
            }

            $hasLaravelSource = ($request['source'] === 'laravel');
            $hasHoneypotReason = isset($request['metadata']['reason']) && $request['metadata']['reason'] === 'honeypot';

            // Strictly verify NO email or user PII is present in metadata
            $metadataStr = json_encode($request['metadata'] ?? []);
            $noPii = !str_contains($metadataStr, 'spambot@example.com') &&
                     !str_contains($metadataStr, 'Spam Bot') &&
                     !str_contains($metadataStr, 'Spam content');

            return $hasLaravelSource && $hasHoneypotReason && $noPii;
        });
    }
}
