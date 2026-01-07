<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\TechStack;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache; // <--- Cache Facade
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\TechStackResource;
use App\Http\Resources\Api\V1\TechStackCollection;
use App\Http\Requests\Api\V1\TechStackStoreRequest;
use App\Http\Requests\Api\V1\TechStackUpdateRequest;

class TechStackController extends BaseController
{
    // Definisikan nama key cache di sini
    private const CACHE_KEY_ALL = 'tech_stacks_all';
    private const CACHE_KEY_SINGLE = 'tech_stack';

    public function index(Request $request)
    {
        // CACHE READ
        $techStacks = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = TechStack::all();
            $data = $this->loadRelationships($data, $request);

            // Simpan sebagai Array murni
            return (new TechStackCollection($data))->resolve();
        });

        return $this->sendResponse($techStacks, "TechStacks retrieved successfully.");
    }

    public function show(Request $request, TechStack $techStack)
    {
        // CACHE READ DETAIL
        $cacheKey = self::CACHE_KEY_SINGLE . '_' . $techStack->id;

        $techStackData = Cache::remember($cacheKey, 3600, function () use ($techStack, $request) {
            $techStack = $this->loadRelationships($techStack, $request);
            return (new TechStackResource($techStack))->resolve();
        });

        return $this->sendResponse($techStackData, "TechStack retrieved successfully.");
    }

    public function store(TechStackStoreRequest $request)
    {
        $request->merge(['slug' => Str::slug($request->name)]);

        $techStack = TechStack::create($request->all());

        // HAPUS CACHE (Panggil method dari BaseController)
        // Parameter: (Key List, Key Prefix Single, ID Model)
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $techStack->id);

        return $this->sendResponse(new TechStackResource($techStack), "TechStack created successfully.");
    }

    public function update(TechStackUpdateRequest $request, TechStack $techStack)
    {
        $request->merge(['slug' => Str::slug($request->name)]);

        $techStack->update($request->validated());

        // HAPUS CACHE
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $techStack->id);

        return $this->sendResponse(new TechStackResource($techStack), "TechStack updated successfully.");
    }

    public function destroy(Request $request, TechStack $techStack)
    {
        $techStack->delete();

        // HAPUS CACHE
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $techStack->id);

        return response()->noContent();
    }
}
