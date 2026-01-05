<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\WorkExperience;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\WorkExperienceResource;
use App\Http\Resources\Api\V1\WorkExperienceCollection;
use App\Http\Requests\Api\V1\WorkExperienceStoreRequest;
use App\Http\Requests\Api\V1\WorkExperienceUpdateRequest;

class WorkExperienceController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $workExperiences = WorkExperience::all();

        $workExperiences = $this->loadRelationships($workExperiences, $request);

        return $this->sendResponse(new WorkExperienceCollection($workExperiences), "Work Experiences retrieved successfully.");
    }

    public function show(Request $request, WorkExperience $workExperience) : JsonResponse
    {
        $workExperience = $this->loadRelationships($workExperience, $request);

        return $this->sendResponse(new WorkExperienceResource($workExperience), "Work Experience retrieved successfully.");
    }

    public function store(WorkExperienceStoreRequest $request)
    {
        $workExperience = WorkExperience::create($request->validated());

        return new WorkExperienceResource($workExperience);
    }


    public function update(WorkExperienceUpdateRequest $request, WorkExperience $workExperience)
    {
        $workExperience->update($request->validated());

        return new WorkExperienceResource($workExperience);
    }

    public function destroy(Request $request, WorkExperience $workExperience)
    {
        $workExperience->delete();

        return response()->noContent();
    }
}
