<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectStoreRequest;
use App\Http\Requests\Api\V1\ProjectUpdateRequest;
use App\Http\Resources\Api\V1\ProjectCollection;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all();
        if ($request->has('with') && $request->filled('with')) {
            $withRelations = explode(',', $request->input('with'));
            $projects->load($withRelations);
        }
        return new ProjectCollection($projects);
    }

    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->validated());

        return new ProjectResource($project);
    }

    public function show(Request $request, Project $project)
    {
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
