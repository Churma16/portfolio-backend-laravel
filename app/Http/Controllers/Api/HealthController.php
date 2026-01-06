<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    public function index()
    {
        // 1. Mulai Stopwatch
        $startTime = microtime(true);

        // 2. Lakukan operasi Redis (Ping atau Get data dummy)
        $redisAlive = false;
        try {
            Redis::connection()->ping();
            $redisAlive = true;
        } catch (\Exception $e) {
            $redisAlive = false;
        }

        // 3. Matikan Stopwatch
        $endTime = microtime(true);

        // 4. Hitung durasi (dalam milidetik)
        // Kita kali 1000 supaya jadi ms
        $processingTime = ($endTime - $startTime) * 1000;

        return response()->json([
            'status' => 'ok',
            'redis_alive' => $redisAlive,
            'server_time' => microtime(true),
            // INI ANGKA AJAIBNYA:
            'redis_processing_time' => round($processingTime, 2) // Bulatkan 2 desimal
        ]);
    }
}
