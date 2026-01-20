<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\TechStackController;
use App\Http\Controllers\Api\V1\WorkExperienceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->name('login');

// Public read routes (index and show) accessible by guest and admin tokens
Route::middleware(['auth:sanctum', 'abilities:guest,admin'])->group(function () {
});
Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('tech-stacks', TechStackController::class)->only(['index', 'show']);
Route::apiResource('tags', TagController::class)->only(['index', 'show']);
Route::apiResource('work-experiences', WorkExperienceController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// Admin write routes (create, store, update, delete)
Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
    Route::apiResource('profiles', ProfileController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('projects', ProjectController::class)->only(['store', 'update', 'destroy']);
    Route::post('projects/{project}/reorder', [ProjectController::class, 'reorder']);
    Route::apiResource('tech-stacks', TechStackController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('tags', TagController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('work-experiences', WorkExperienceController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('messages', MessageController::class)->only(['store', 'update', 'destroy']);
    Route::post('/generate-guest-token', [AuthController::class, 'generateGuestToken']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Guest token routes
Route::middleware(['auth:sanctum', 'abilities:guest'])->group(function () {
//    Route::get('messages', [MessageController::class, 'index']);
    Route::get('messages/{message}', [MessageController::class, 'show']);
});

Route::get('/health', [HealthController::class, 'index']);
