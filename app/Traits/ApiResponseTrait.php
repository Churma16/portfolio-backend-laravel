<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function sendResponse($result, $message = 'Success')
    {
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => $message,
            ],
            'data' => $result,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function sendError($error, $code = 404)
    {
        return response()->json([
            'meta' => [
                'code' => $code,
                'status' => 'error',
                'message' => $error,
            ],
            'data' => null,
        ], $code);
    }
}
