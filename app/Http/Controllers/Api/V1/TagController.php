<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache; // <--- Import Cache Facade
use App\Http\Resources\Api\V1\TagResource;
use App\Http\Resources\Api\V1\TagCollection;
use App\Http\Requests\Api\V1\TagStoreRequest;
use App\Http\Requests\Api\V1\TagUpdateRequest;
use App\Http\Controllers\Api\V1\BaseController;

class TagController extends BaseController
{
    // Konstanta key cache untuk daftar semua tags
    private const CACHE_KEY_ALL = 'tags_all';

    public function index(Request $request)
    {
        // CACHE READ: Ambil list semua tags
        $tags = Cache::remember(self::CACHE_KEY_ALL, 3600, function () use ($request) {
            $data = Tag::all();
            $data = $this->loadRelationships($data, $request);

            // OPTIMASI: Simpan sebagai Array murni agar ringan di Redis
            return (new TagCollection($data))->resolve();
        });

        return $this->sendResponse($tags, "Tags retrieved successfully.");
    }

    public function show(Request $request, Tag $tag)
    {
        // Cache Key spesifik per ID: tag_1, tag_2, dst
        $cacheKey = 'tag_' . $tag->id;

        $tagData = Cache::remember($cacheKey, 3600, function () use ($tag, $request) {
            $tag = $this->loadRelationships($tag, $request);

            // OPTIMASI: Simpan sebagai Array murni
            return (new TagResource($tag))->resolve();
        });

        return $this->sendResponse($tagData, "Tag retrieved successfully.");
    }

    public function store(TagStoreRequest $request)
    {
        // Generate slug manual (sesuai logika kode asli Anda)
        $request->merge(['slug' => Str::slug($request->name)]);

        $tag = Tag::create($request->all());

        // Hapus Cache List Utama karena ada item baru
        $this->clearTagCache();

        return new TagResource($tag);
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        // Generate slug manual jika nama berubah
        if ($request->has('name')) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        $tag->update($request->all()); // Gunakan all() atau validated() sesuai kebutuhan logic slug

        // Hapus Cache List Utama & Cache Item Detail ini
        $this->clearTagCache($tag->id);

        return new TagResource($tag);
    }

    public function destroy(Request $request, Tag $tag)
    {
        $tag->delete();

        // Hapus Cache List Utama & Cache Item Detail ini
        $this->clearTagCache($tag->id);

        return response()->noContent();
    }

    /**
     * Helper untuk menghapus cache Tag
     * @param int|null $tagId
     */
    private function clearTagCache($tagId = null)
    {
        // 1. Hapus list utama agar index() mengambil data terbaru
        Cache::forget(self::CACHE_KEY_ALL);

        // 2. Jika ada ID spesifik (saat update/delete), hapus cache detailnya juga
        if ($tagId) {
            Cache::forget('tag_' . $tagId);
        }
    }
}
