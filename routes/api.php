<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\TechStackController;
use App\Http\Controllers\Api\V1\WorkExperienceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('profiles', ProfileController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tech-stacks', TechStackController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('work-experiences', WorkExperienceController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('/health', [HealthController::class, 'index']);
    Route::post('/generate-guest-token', [AuthController::class, 'generateGuestToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
