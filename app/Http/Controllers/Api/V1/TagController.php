<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TagResource;
use App\Http\Resources\Api\V1\TagCollection;
use App\Http\Requests\Api\V1\TagStoreRequest;
use App\Http\Requests\Api\V1\TagUpdateRequest;
use App\Http\Controllers\Api\V1\BaseController;

class TagController extends BaseController
{
    public function index(Request $request)
    {
        $tags = Tag::all();
        $tags = $this->loadRelationships($tags, $request);

        return $this->sendResponse(new TagCollection($tags), "Tags retrieved successfully.");
    }

    public function show(Request $request, Tag $tag)
    {
        $tag = $this->loadRelationships($tag, $request);

        return $this->sendResponse(new TagResource($tag), "Tag retrieved successfully.");
    }

    public function store(TagStoreRequest $request)
    {

        $request->merge(['slug' => Str::slug($request->name)]);
        // return response()->json(['message' => $request->all()]);
        $tag = Tag::create($request->all());

        return new TagResource($tag);
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $request->merge(['slug' => Str::slug($request->name)]);
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    public function destroy(Request $request, Tag $tag)
    {
        $tag->delete();

        return response()->noContent();
    }
}
