<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProjectCollection;
use App\Http\Requests\Api\V1\ProjectStoreRequest;
use App\Http\Requests\Api\V1\ProjectUpdateRequest;

class ProjectController extends BaseController
{
    public function index(Request $request)
    {
        $projects = Project::orderBy('column_order')->get();

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
        $data = $request->validated();
        $slug = Str::slug($data['title']);
        $data['slug'] = $slug;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('projects', 'public');
            $data['thumbnail'] = $path;
        }

        $project = Project::create($data);

        return $this->sendResponse(new ProjectResource($project), "Project created successfully.");
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $data = $request->validated();
        $slug = Str::slug($data['title']);
        $data['slug'] = $slug;

        if ($request->hasFile('thumbnail')) {
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }
            $path = $request->file('thumbnail')->store('projects', 'public');
            $data['thumbnail'] = $path;
        }
        // return response()->json($request->category_id);

        $data['category_id'] = $request->category_id;


        // Update data dasar
        $project->update($data);

        // Update Hubungan (Sync akan otomatis hapus yang tidak dipilih & tambah yang baru)
        if ($request->has('tech_stack_ids')) {
            $techStackIds = $request->tech_stack_ids;
            if (is_string($techStackIds)) {
                $techStackIds = json_decode($techStackIds, true);
            }
            $project->techStacks()->sync($techStackIds);
        }

        if ($request->has('tag_ids')) {
            $tagIds = $request->tag_ids;
            if (is_string($tagIds)) {
                $tagIds = json_decode($tagIds, true);
            }
            $project->tags()->sync($tagIds);
        }


        return $this->sendResponse(new ProjectResource($project), "Project updated successfully.");
    }

    public function destroy(Request $request, Project $project)
    {
        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }

        $projectOrder = $project->column_order;
        Project::where('column_order', '>', $projectOrder)->decrement('column_order');


        $project->delete();

        return response()->noContent();
    }

    public function reorder(Request $request, Project $project)
    {

        if (!$request->has('direction')) {
            return $this->sendError("Direction is required.", [], Response::HTTP_BAD_REQUEST);
        }

        if ($request->direction == 'up') {
            $previousProject = Project::where('column_order', '<', $project->column_order)
                ->orderBy('column_order', 'desc')
                ->first();

            if ($previousProject) {
                $currentOrder = $project->column_order;
                $project->column_order = $previousProject->column_order;
                $previousProject->column_order = $currentOrder;

                $project->save();
                $previousProject->save();
            }
        } elseif ($request->direction == 'down') {
            $nextProject = Project::where('column_order', '>', $project->column_order)
                ->orderBy('column_order', 'asc')
                ->first();

            if ($nextProject) {
                $currentOrder = $project->column_order;
                $project->column_order = $nextProject->column_order;
                $nextProject->column_order = $currentOrder;

                $project->save();
                $nextProject->save();
            }
        } else {
            return $this->sendError("Invalid direction value. Use 'up' or 'down'.", [], Response::HTTP_BAD_REQUEST);
        }
    }
}
