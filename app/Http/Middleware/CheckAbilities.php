<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAbilities extends Middleware
{
    public function handle(Request $request, Closure $next, ...$abilities): Response
    {
        if (!$request->user() || !$request->user()->tokenCan(...$abilities)) {
            return response()->json([
                'meta' => [
                    'code' => 403,
                    'status' => 'error',
                    'message' => 'Unauthorized. Insufficient permissions.',
                ],
            ], 403);
        }

        return $next($request);
    }
}
