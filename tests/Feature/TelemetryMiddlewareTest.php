<?php

namespace Tests\Feature;

use App\Http\Middleware\TelemetryMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TelemetryMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function telemetry_is_disabled_when_monitor_url_is_not_configured(): void
    {
        Http::fake();
        Config::set('services.monitor.url', null);
        putenv('MONITOR_URL');

        $response = $this->getJson('/api/health');

        $response->assertOk();
        Http::assertNothingSent();
    }

    #[Test]
    public function telemetry_records_and_dispatches_http_request_metric_on_terminate(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            $monitorUrl => Http::response(['status' => 'recorded'], 200),
        ]);

        $response = $this->getJson('/api/health');

        $response->assertOk();

        Http::assertSent(function ($request) use ($monitorUrl) {
            return $request->url() === $monitorUrl &&
                   $request['source'] === 'laravel' &&
                   $request['type'] === 'http_request' &&
                   ($request['endpoint'] === 'api/health' || $request['endpoint'] === '/api/health') &&
                   $request['status_code'] === 200 &&
                   isset($request['duration_ms']) &&
                   is_numeric($request['duration_ms']) &&
                   $request['duration_ms'] >= 0;
        });
    }

    #[Test]
    public function telemetry_records_unrouted_paths_with_correct_status(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            $monitorUrl => Http::response(['status' => 'recorded'], 200),
        ]);

        $response = $this->getJson('/api/nonexistent-telemetry-test-route');

        $response->assertNotFound();

        Http::assertSent(function ($request) use ($monitorUrl) {
            return $request->url() === $monitorUrl &&
                   $request['source'] === 'laravel' &&
                   $request['type'] === 'http_request' &&
                   str_contains($request['endpoint'], 'nonexistent-telemetry-test-route') &&
                   $request['status_code'] === 404 &&
                   isset($request['duration_ms']);
        });
    }

    #[Test]
    public function telemetry_swallows_unreachable_monitor_exceptions_gracefully(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            $monitorUrl => function () {
                throw new ConnectionException('Failed to connect to monitor: Connection refused');
            },
        ]);

        $response = $this->getJson('/api/health');

        $response->assertOk();
    }

    #[Test]
    public function dispatch_helper_sends_custom_event_with_laravel_source(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            $monitorUrl => Http::response(['status' => 'recorded'], 200),
        ]);

        TelemetryMiddleware::dispatch([
            'type' => 'custom_action',
            'metadata' => ['key' => 'value'],
        ]);

        Http::assertSent(function ($request) use ($monitorUrl) {
            return $request->url() === $monitorUrl &&
                   $request['source'] === 'laravel' &&
                   $request['type'] === 'custom_action' &&
                   $request['metadata']['key'] === 'value';
        });
    }
}
