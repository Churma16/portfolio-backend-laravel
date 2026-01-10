<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\WorkExperienceStoreRequest;
use App\Http\Requests\Api\V1\WorkExperienceUpdateRequest;
use App\Http\Resources\Api\V1\WorkExperienceResource;
use App\Models\WorkExperience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WorkExperienceController extends BaseController
{
    private const CACHE_KEY_ALL = 'work_experiences_all';
    private const CACHE_KEY_SINGLE = 'work_experience';

    public function index(Request $request): JsonResponse
    {
        $data = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = WorkExperience::get();
            $data = $this->loadRelationships($data, $request);

            return WorkExperienceResource::collection($data)->resolve();
        });

        return $this->sendResponse($data, "Work Experiences retrieved successfully.");
    }

    public function show(Request $request, WorkExperience $workExperience): JsonResponse
    {
        $cacheKey = self::CACHE_KEY_SINGLE . '_' . $workExperience->id;

        $workExperienceData = Cache::remember($cacheKey, 3600, function () use ($workExperience, $request) {
            $workExperience = $this->loadRelationships($workExperience, $request);
            return (new WorkExperienceResource($workExperience))->resolve();
        });

        return $this->sendResponse($workExperienceData, "Work Experience retrieved successfully.");
    }

    public function store(WorkExperienceStoreRequest $request)
    {
        $workExperience = WorkExperience::create($request->validated());

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE);

        return $this->sendResponse(new WorkExperienceResource($workExperience), "Work Experience created successfully.");
    }

    public function update(WorkExperienceUpdateRequest $request, WorkExperience $workExperience)
    {
        if ($request->has('tech_stack_ids')) {
            $techStackIds = is_string($request->tech_stack_ids)
                ? json_decode($request->tech_stack_ids, true)
                : $request->tech_stack_ids;
            $workExperience->techStacks()->sync($techStackIds);
        }

        if ($request->has('tag_ids')) {
            $tagIds = is_string($request->tag_ids)
                ? json_decode($request->tag_ids, true)
                : $request->tag_ids;
            $workExperience->tags()->sync($tagIds);
        }

        $workExperience->update($request->validated());

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $workExperience->id);

        return $this->sendResponse(new WorkExperienceResource($workExperience), "Work Experience updated successfully.");
    }

    public function destroy(Request $request, WorkExperience $workExperience)
    {
        $workExperience->delete();

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $workExperience->id);

        return response()->noContent();
    }
}
