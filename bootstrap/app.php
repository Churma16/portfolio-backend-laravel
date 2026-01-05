<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'meta' => [
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'Resource not found (Record does not exist)',
                    ],
                    'data' => [
                        'details' => $e->getMessage()
                    ],
                ], 404);
            }
        });
        $exceptions->render(function (RelationNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'meta' => [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'The requested relationship does not exist.',
                    ],
                    'data' => [
                        'details' => $e->getMessage()
                    ],
                ], 400);
            }
        });
    })->create();
