<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class TelemetryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $monitorUrl = config('services.monitor.url') ?: env('MONITOR_URL');
        if (empty($monitorUrl)) {
            return;
        }

        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $request->server('REQUEST_TIME_FLOAT', microtime(true));
        $duration = (microtime(true) - $startTime) * 1000;

        $endpoint = $request->route() ? $request->route()->uri() : $request->path();

        try {
            Http::timeout(0.1)
                ->withoutVerifying()
                ->post($monitorUrl, [
                    'source' => 'laravel',
                    'type' => 'http_request',
                    'endpoint' => $endpoint,
                    'status_code' => $response->getStatusCode(),
                    'duration_ms' => round($duration, 2),
                ]);
        } catch (\Throwable $e) {
            // Silently swallow exceptions for non-blocking fire-and-forget telemetry
        }
    }

    /**
     * Dispatch fire-and-forget telemetry event to MONITOR_URL.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function dispatch(array $payload): void
    {
        $monitorUrl = config('services.monitor.url') ?: env('MONITOR_URL');
        if (empty($monitorUrl)) {
            return;
        }

        try {
            Http::timeout(0.1)
                ->withoutVerifying()
                ->post($monitorUrl, array_merge(['source' => 'laravel'], $payload));
        } catch (\Throwable $e) {
            // Silently swallow exceptions
        }
    }
}
