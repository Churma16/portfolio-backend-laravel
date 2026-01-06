<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\TechStack;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\TechStackResource;
use App\Http\Resources\Api\V1\TechStackCollection;
use App\Http\Requests\Api\V1\TechStackStoreRequest;
use App\Http\Requests\Api\V1\TechStackUpdateRequest;

class TechStackController extends BaseController
{
    public function index(Request $request)
    {
        $techStacks = TechStack::all();

        $techStacks = $this->loadRelationships($techStacks, $request);

        return $this->sendResponse(new TechStackCollection($techStacks), "TechStacks retrieved successfully.");
    }

    public function show(Request $request, TechStack $techStack)
    {
        $techStack = $this->loadRelationships($techStack, $request);

        return $this->sendResponse(new TechStackResource($techStack), "TechStack retrieved successfully.");
    }

    public function store(TechStackStoreRequest $request)
    {
        // $data = $request->validate(['name' => 'required', 'icon_name' => 'nullable']);
        $request->merge(['slug' => Str::slug($request->name)]);
        // $request->merge(['icon' => $request->icon_url ?? null]);
        // $request->remove('icon_url');
        // return response()->json(['message' => $request->all()]);
        $techStack = TechStack::create($request->all());

        return $this->sendResponse(new TechStackResource($techStack), "TechStack created successfully.");

        // $data = $request->validate(['name' => 'required', 'icon_name' => 'nullable']);
        // TechStack::create($data);
        // return response()->json(['message' => 'Saved']);
    }


    public function update(TechStackUpdateRequest $request, TechStack $techStack)
    {
        // return response()->json(['message' => $request->all()]);

        $request->merge(['slug' => Str::slug($request->name)]);

        $techStack->update($request->validated());

        return $this->sendResponse(new TechStackResource($techStack), "TechStack updated successfully.");
    }

    public function destroy(Request $request, TechStack $techStack)
    {
        $techStack->delete();

        return response()->noContent();
    }
}
