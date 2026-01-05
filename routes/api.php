<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TechStackController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\WorkExperienceController;
use App\Http\Controllers\Api\V1\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('profiles', ProfileController::class)->middleware('auth:sanctum');

Route::apiResource('projects', ProjectController::class)->middleware('auth:sanctum');

Route::apiResource('tech-stacks', TechStackController::class)->middleware('auth:sanctum');
Route::apiResource('tags', TagController::class)->middleware('auth:sanctum');

Route::apiResource('work-experiences', WorkExperienceController::class)->middleware('auth:sanctum');

Route::apiResource('categories', CategoryController::class)->middleware('auth:sanctum');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
