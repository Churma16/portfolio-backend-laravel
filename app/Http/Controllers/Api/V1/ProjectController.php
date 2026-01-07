<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProjectCollection;
use App\Http\Requests\Api\V1\ProjectStoreRequest;
use App\Http\Requests\Api\V1\ProjectUpdateRequest;

class ProjectController extends BaseController
{
    private const CACHE_KEY_ALL = 'projects_all';

    public function index(Request $request)
    {
        // CACHE: Simpan hasil 'resolve' (Array Murni) ke Redis
        $projects = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = Project::orderBy('column_order')->get();
            $data = $this->loadRelationships($data, $request);

            // OPTIMASI: Ubah Resource menjadi Array sebelum disimpan
            return ProjectResource::collection($data)->resolve();
        });

        // Karena $projects sudah berupa array (bukan Object Eloquent lagi),
        // Kita langsung kirim ke sendResponse.
        return $this->sendResponse($projects, "Projects retrieved successfully.");
    }

    public function show(Request $request, Project $project)
    {
        $cacheKey = 'project_' . $project->id;

        $projectData = Cache::remember($cacheKey, 3600, function () use ($project, $request) {
            $project = $this->loadRelationships($project, $request);

            // OPTIMASI: Ubah Resource menjadi Array sebelum disimpan
            return (new ProjectResource($project))->resolve();
        });

        return $this->sendResponse($projectData, "Project retrieved successfully.");
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

        // Hapus Cache agar index terupdate
        $this->clearProjectCache();

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

        $data['category_id'] = $request->category_id;

        $project->update($data);

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

        // Hapus Cache Global & Spesifik ID
        $this->clearProjectCache($project->id);

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

        // Hapus Cache
        $this->clearProjectCache($project->id);

        return response()->noContent();
    }

    public function reorder(Request $request, Project $project)
    {
        if (!$request->has('direction')) {
            return $this->sendError("Direction is required.", [], Response::HTTP_BAD_REQUEST);
        }

        $isUpdated = false;

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
                $isUpdated = true;
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
                $isUpdated = true;
            }
        } else {
            return $this->sendError("Invalid direction value. Use 'up' or 'down'.", [], Response::HTTP_BAD_REQUEST);
        }

        if ($isUpdated) {
            // Cukup clear list utama karena urutan berubah
            $this->clearProjectCache();
        }

        return $this->sendResponse([], "Project reordered successfully.");
    }

    /**
     * Helper: Clear Cache
     */
    private function clearProjectCache($projectId = null)
    {
        Cache::forget(self::CACHE_KEY_ALL);

        if ($projectId) {
            Cache::forget('project_' . $projectId);
        }
    }
}
