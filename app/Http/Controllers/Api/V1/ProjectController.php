<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProjectCollection;
use App\Http\Requests\Api\V1\ProjectStoreRequest;
use App\Http\Requests\Api\V1\ProjectUpdateRequest;

class ProjectController extends BaseController
{
    public function index(Request $request)
    {
        $projects = Project::all();
        $projects = $this->loadRelationships($projects, $request);

        return $this->sendResponse(new ProjectCollection($projects), "Projects retrieved successfully.");
    }

    public function show(Request $request, Project $project)
    {
        $project = $this->loadRelationships($project, $request);


        return $this->sendResponse(new ProjectResource($project), "Project retrieved successfully.");
    }

    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->validated());

        return new ProjectResource($project);
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $project->update($request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Request $request, Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
