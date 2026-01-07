<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class BaseController extends Controller
{
    use ApiResponseTrait;
    protected function loadRelationships($data, Request $request)
    {
        if ($request->has('with') && $request->filled('with')) {
            $withRelations = explode(',', $request->input('with'));

            return $data->load($withRelations);
        }

        return $data;
    }

    protected function clearCache(string $listKey, string $singleKeyPrefix, ?int $modelId = null)
    {
        // 1. Hapus cache list utama
        Cache::forget($listKey);

        // 2. Hapus cache detail item jika ID diberikan
        if ($modelId) {
            Cache::forget($singleKeyPrefix . '_' . $modelId);
        }
    }
}
