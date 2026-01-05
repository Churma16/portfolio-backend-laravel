<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WorkExperienceStoreRequest;
use App\Http\Requests\Api\V1\WorkExperienceUpdateRequest;
use App\Http\Resources\Api\V1\WorkExperienceCollection;
use App\Http\Resources\Api\V1\WorkExperienceResource;
use App\Models\WorkExperience;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkExperienceController extends Controller
{
    public function index(Request $request)
    {
        $workExperiences = WorkExperience::all();

        
        return new WorkExperienceCollection($workExperiences);
    }

    public function store(WorkExperienceStoreRequest $request)
    {
        $workExperience = WorkExperience::create($request->validated());

        return new WorkExperienceResource($workExperience);
    }

    public function show(Request $request, WorkExperience $workExperience)
    {
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
