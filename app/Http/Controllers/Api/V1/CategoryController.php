<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\CategoryStoreRequest;
use App\Http\Requests\Api\V1\CategoryUpdateRequest;
use App\Http\Resources\Api\V1\CategoryCollection;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends BaseController
{
    // Definisikan DUA key ini agar clean
    private const CACHE_KEY_ALL = 'categories:list:all';
    private const CACHE_KEY_SINGLE = 'categories:single';

    public function index(Request $request)
    {
        // CACHE READ LIST
        $categories = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = Category::all();
            $data = $this->loadRelationships($data, $request);

            return (new CategoryCollection($data))->resolve();
        });

        return $this->sendResponse($categories, "Categories retrieved successfully.");
    }

    public function show(Request $request, Category $category)
    {
        // CACHE READ DETAIL
        // Menggunakan key prefix 'categories:single' + ID -> 'categories:single:1'
        $cacheKey = self::CACHE_KEY_SINGLE . ':' . $category->id;

        $categoryData = Cache::remember($cacheKey, 3600, function () use ($category, $request) {
            $category = $this->loadRelationships($category, $request);
            return (new CategoryResource($category))->resolve();
        });

        return $this->sendResponse($categoryData, "Category retrieved successfully.");
    }

    public function store(CategoryStoreRequest $request)
    {

        $category = Category::create($request->validated());

        // INHERITANCE: Panggil fungsi dari BaseController
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $category->id);

        return new CategoryResource($category);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {

        $category->update($request->validated());

        // INHERITANCE: Clear cache
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $category->id);

        return new CategoryResource($category);
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        // INHERITANCE: Clear cache
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $category->id);

        return response()->noContent();
    }
}
