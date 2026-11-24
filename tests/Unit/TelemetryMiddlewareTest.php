<?php

namespace Tests\Unit;

use App\Http\Middleware\TelemetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class TelemetryMiddlewareTest extends TestCase
{
    #[Test]
    public function handle_passes_request_to_next_closure(): void
    {
        $middleware = new TelemetryMiddleware;
        $request = Request::create('/api/health', 'GET');
        $expectedResponse = new Response('ok', 200);

        $response = $middleware->handle($request, function ($req) use ($request, $expectedResponse) {
            $this->assertSame($request, $req);

            return $expectedResponse;
        });

        $this->assertSame($expectedResponse, $response);
    }

    #[Test]
    public function terminate_is_noop_when_monitor_url_is_null(): void
    {
        Http::fake();
        Config::set('services.monitor.url', null);
        putenv('MONITOR_URL');

        $middleware = new TelemetryMiddleware;
        $request = Request::create('/api/health', 'GET');
        $response = new Response('ok', 200);

        $middleware->terminate($request, $response);

        Http::assertNothingSent();
    }

    #[Test]
    public function terminate_dispatches_expected_payload(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            $monitorUrl => Http::response(['status' => 'ok'], 200),
        ]);

        $middleware = new TelemetryMiddleware;
        $request = Request::create('/api/projects', 'GET');
        $response = new Response('{"data":[]}', 200);

        $middleware->terminate($request, $response);

        Http::assertSent(function ($req) use ($monitorUrl) {
            return $req->url() === $monitorUrl &&
                   $req['source'] === 'laravel' &&
                   $req['type'] === 'http_request' &&
                   str_contains($req['endpoint'], 'projects') &&
                   $req['status_code'] === 200 &&
                   is_numeric($req['duration_ms']) &&
                   $req['duration_ms'] >= 0;
        });
    }

    #[Test]
    public function dispatch_handles_connection_exceptions_without_failing(): void
    {
        $monitorUrl = 'http://portfolio-monitor:8080/api/events';
        Config::set('services.monitor.url', $monitorUrl);

        Http::fake([
            $monitorUrl => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            },
        ]);

        // Must not throw any exception
        TelemetryMiddleware::dispatch(['type' => 'test_event']);
        $this->assertTrue(true);
    }
}
