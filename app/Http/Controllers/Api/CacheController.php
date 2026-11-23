<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheController extends Controller
{
    use ApiResponseTrait;

    public function flush(Request $request)
    {
        $prefix = $request->input('prefix', 'all');

        if ($prefix === 'all' || empty($prefix)) {
            Cache::flush();
        } else {
            try {
                $redisConnection = Redis::connection('cache');
                $configuredPrefix = config('database.redis.options.prefix', '');
                $searchPattern = $configuredPrefix . '*' . $prefix . '*';
                $matchedKeys = $redisConnection->keys($searchPattern);

                if (!empty($matchedKeys)) {
                    foreach ($matchedKeys as $matchedKey) {
                        $cleanKey = $configuredPrefix !== '' && str_starts_with($matchedKey, $configuredPrefix)
                            ? substr($matchedKey, strlen($configuredPrefix))
                            : $matchedKey;
                        Cache::forget($cleanKey);
                    }
                }
            } catch (\Exception $exception) {
                // Fallback to literal forget if Redis connection is not available
                Cache::forget($prefix);
            }
        }

        return $this->sendResponse([
            'backend' => 'laravel',
            'prefix' => $prefix,
            'flushed_at' => now()->toIso8601String(),
        ], 'Laravel cache flushed successfully');
    }
}
