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

        return $this->sendResponse(new ProjectResource($project), "Project created successfully.");
    }

    public function update(Request $request, Project $project)
    {
        // Update data dasar
        $project->update($request->only(['title', 'content', 'thumbnail', 'repo_url', 'demo_url']));

        // Update Hubungan (Sync akan otomatis hapus yang tidak dipilih & tambah yang baru)
        if ($request->has('tech_stack_ids')) {
            $project->techStacks()->sync($request->tech_stack_ids);
        }

        if ($request->has('tag_ids')) {
            $project->tags()->sync($request->tag_ids);
        }

        return response()->json(['message' => 'Updated']);
    }

    public function destroy(Request $request, Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
