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
    private const CACHE_KEY_ALL = 'workExperiences:list:all';
    private const CACHE_KEY_SINGLE = 'workExperiences:single';

    public function index(Request $request): JsonResponse
    {
        $data = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = WorkExperience::orderBy('column_order')->get();
            $data = $this->loadRelationships($data, $request);

            return WorkExperienceResource::collection($data)->resolve();
        });

        return $this->sendResponse($data, "Work Experiences retrieved successfully.");
    }

    public function show(Request $request, WorkExperience $workExperience): JsonResponse
    {
        $cacheKey = self::CACHE_KEY_SINGLE . ':' . $workExperience->id;

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
        $workExperienceOrder = $workExperience->column_order;
        WorkExperience::where('column_order', '>', $workExperienceOrder)->decrement('column_order');

        $workExperience->delete();

        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $workExperience->id);

        return response()->noContent();
    }

    public function reorder(Request $request, WorkExperience $workExperience)
    {
        if (!$request->has('direction')) {
            return $this->sendError("Direction is required.", [], 400);
        }

        $isUpdated = false;
        if ($request->direction == 'up') {
            $previousWorkExperience = WorkExperience::where('column_order', '<', $workExperience->column_order)
                ->orderBy('column_order', 'desc')
                ->first();

            if ($previousWorkExperience) {
                $currentOrder = $workExperience->column_order;
                $workExperience->column_order = $previousWorkExperience->column_order;
                $previousWorkExperience->column_order = $currentOrder;
                $workExperience->save();
                $previousWorkExperience->save();
                $isUpdated = true;
            }
        } elseif ($request->direction == 'down') {
            $nextWorkExperience = WorkExperience::where('column_order', '>', $workExperience->column_order)
                ->orderBy('column_order', 'asc')
                ->first();

            if ($nextWorkExperience) {
                $currentOrder = $workExperience->column_order;
                $workExperience->column_order = $nextWorkExperience->column_order;
                $nextWorkExperience->column_order = $currentOrder;
                $workExperience->save();
                $nextWorkExperience->save();
                $isUpdated = true;
            }
        }

        if ($isUpdated) {
            $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE);
        }

        return $this->sendResponse([], "Work Experience reordered successfully.");
    }
}
