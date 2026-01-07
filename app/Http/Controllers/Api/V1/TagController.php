<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Api\V1\TagResource;
use App\Http\Resources\Api\V1\TagCollection;
use App\Http\Requests\Api\V1\TagStoreRequest;
use App\Http\Requests\Api\V1\TagUpdateRequest;
use App\Http\Controllers\Api\V1\BaseController;

class TagController extends BaseController
{
    // Konstanta key cache agar mudah dikelola
    private const CACHE_KEY_ALL = 'tags_all';
    private const CACHE_KEY_SINGLE = 'tag';

    public function index(Request $request)
    {
        // CACHE READ: Mengambil semua tags
        $tags = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = Tag::all();
            $data = $this->loadRelationships($data, $request);

            return (new TagCollection($data))->resolve();
        });

        return $this->sendResponse($tags, "Tags retrieved successfully.");
    }

    public function show(Request $request, Tag $tag)
    {
        // CACHE READ: Mengambil satu tag spesifik (prefix: tag_)
        $cacheKey = self::CACHE_KEY_SINGLE . '_' . $tag->id;

        $tagData = Cache::remember($cacheKey, 3600, function () use ($tag, $request) {
            $tag = $this->loadRelationships($tag, $request);

            return (new TagResource($tag))->resolve();
        });

        return $this->sendResponse($tagData, "Tag retrieved successfully.");
    }

    public function store(TagStoreRequest $request)
    {
        $request->merge(['slug' => Str::slug($request->name)]);

        $tag = Tag::create($request->all());

        // Panggil method clearCache dari BaseController
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $tag->id);

        return new TagResource($tag);
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        if ($request->has('name')) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        $tag->update($request->all());

        // Hapus cache lama (list & item spesifik)
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $tag->id);

        return new TagResource($tag);
    }

    public function destroy(Request $request, Tag $tag)
    {
        $tagId = $tag->id; // Simpan ID sebelum didelete
        $tag->delete();

        // Hapus cache lama
        $this->clearCache(self::CACHE_KEY_ALL, self::CACHE_KEY_SINGLE, $tagId);

        return response()->noContent();
    }
}
