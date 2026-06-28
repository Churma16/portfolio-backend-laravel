<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\TechStackCategoryStoreRequest;
use App\Http\Requests\Api\V1\TechStackCategoryUpdateRequest;
use App\Http\Resources\Api\V1\TechStackCategoryCollection;
use App\Http\Resources\Api\V1\TechStackCategoryResource;
use App\Models\TechStackCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TechStackCategoryController extends BaseController
{
    private const CACHE_KEY_ALL = 'tech_stack_categories:list:all';
    private const CACHE_KEY_SINGLE = 'tech_stack_categories:single';

    public function index(Request $request)
    {
        $categories = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = TechStackCategory::all();
            $data = $this->loadRelationships($data, $request);

            return (new TechStackCategoryCollection($data))->resolve();
        });

        return $this->sendResponse($categories, "Tech Stack Categories retrieved successfully.");
    }

    public function show(Request $request, TechStackCategory $techStackCategory)
    {
        $cacheKey = self::CACHE_KEY_SINGLE . ':' . $techStackCategory->id;

        $categoryData = Cache::remember($cacheKey, 3600, function () use ($techStackCategory, $request) {
            $techStackCategory = $this->loadRelationships($techStackCategory, $request);
            return (new TechStackCategoryResource($techStackCategory))->resolve();
        });

        return $this->sendResponse($categoryData, "Tech Stack Category retrieved successfully.");
    }

    public function store(TechStackCategoryStoreRequest $request)
    {
        $techStackCategory = TechStackCategory::create($request->validated());

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $techStackCategory->id);

        // Also clear tech stacks list cache since relationship cache might be affected
        Cache::forget('tech_stacks:list:all');

        return new TechStackCategoryResource($techStackCategory);
    }

    public function update(TechStackCategoryUpdateRequest $request, TechStackCategory $techStackCategory)
    {
        $techStackCategory->update($request->validated());

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $techStackCategory->id);
        
        Cache::forget('tech_stacks:list:all');

        return new TechStackCategoryResource($techStackCategory);
    }

    public function destroy(Request $request, TechStackCategory $techStackCategory)
    {
        $techStackCategory->delete();

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $techStackCategory->id);
        
        Cache::forget('tech_stacks:list:all');

        return response()->noContent();
    }
}
