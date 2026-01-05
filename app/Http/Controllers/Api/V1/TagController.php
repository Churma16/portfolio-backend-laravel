<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TagStoreRequest;
use App\Http\Requests\Api\V1\TagUpdateRequest;
use App\Http\Resources\Api\V1\TagCollection;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::all();

        return new TagCollection($tags);
    }

    public function store(TagStoreRequest $request)
    {
        $tag = Tag::create($request->validated());

        return new TagResource($tag);
    }

    public function show(Request $request, Tag $tag)
    {
        return new TagResource($tag);
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    public function destroy(Request $request, Tag $tag)
    {
        $tag->delete();

        return response()->noContent();
    }
}
