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
    // Definisikan konstanta untuk kemudahan maintenance
    private const CACHE_KEY_ALL = 'projects:list:all';
    private const CACHE_KEY_SINGLE = 'projects:single';

    public function index(Request $request)
    {
        $projects = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = Project::orderBy('column_order')->get();
            $data = $this->loadRelationships($data, $request);

            return ProjectResource::collection($data)->resolve();
        });

        return $this->sendResponse($projects, "Projects retrieved successfully.");
    }

    public function show(Request $request, Project $project)
    {
        $cacheKey = self::CACHE_KEY_SINGLE . ':' . $project->id;

        $projectData = Cache::remember($cacheKey, 3600, function () use ($project, $request) {
            $project = $this->loadRelationships($project, $request);
            return (new ProjectResource($project))->resolve();
        });

        return $this->sendResponse($projectData, "Project retrieved successfully.");
    }

    public function store(ProjectStoreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('projects', 'public');
        }

        $project = Project::create($data);


        if ($request->has('tech_stack_ids')) {
            $techStackIds = is_string($request->tech_stack_ids)
                ? json_decode($request->tech_stack_ids, true)
                : $request->tech_stack_ids;
            $project->techStacks()->sync($techStackIds);
        }

        if ($request->has('tag_ids')) {
            $tagIds = is_string($request->tag_ids)
                ? json_decode($request->tag_ids, true)
                : $request->tag_ids;
            $project->tags()->sync($tagIds);
        }

        // Gunakan inherit method dari BaseController
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE);

        return $this->sendResponse(new ProjectResource($project), "Project created successfully.");
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('thumbnail')) {
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('projects', 'public');
        }

        $data['category_id'] = $request->category_id;
        $project->update($data);

        if ($request->has('tech_stack_ids')) {
            $techStackIds = is_string($request->tech_stack_ids)
                ? json_decode($request->tech_stack_ids, true)
                : $request->tech_stack_ids;
            $project->techStacks()->sync($techStackIds);
        }

        if ($request->has('tag_ids')) {
            $tagIds = is_string($request->tag_ids)
                ? json_decode($request->tag_ids, true)
                : $request->tag_ids;
            $project->tags()->sync($tagIds);
        }

        // Gunakan inherit method dari BaseController
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $project->id);

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

        // Gunakan inherit method dari BaseController
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $project->id);

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
        }

        if ($isUpdated) {
            // Cukup hapus list utama karena urutan berubah,
            // ID detail tetap sama jadi tidak wajib dihapus (tapi aman juga kalau dihapus)
            $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE);
        }

        return $this->sendResponse([], "Project reordered successfully.");
    }
}
