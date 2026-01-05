<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('profiles', App\Http\Controllers\Api\V1\ProfileController::class);

Route::apiResource('projects', App\Http\Controllers\Api\V1\ProjectController::class);

Route::apiResource('tech-stacks', App\Http\Controllers\Api\V1\TechStackController::class);

Route::apiResource('tags', App\Http\Controllers\Api\V1\TagController::class);

Route::apiResource('work-experiences', App\Http\Controllers\Api\V1\WorkExperienceController::class);

Route::apiResource('categories', App\Http\Controllers\Api\V1\CategoryController::class);
