<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function loadRelationships($data, Request $request)
    {
        if ($request->has('with') && $request->filled('with')) {
            $withRelations = explode(',', $request->input('with'));

            return $data->load($withRelations);
        }

        return $data;
    }

    public function sendResponse($result, $message = 'Success')
    {
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => $message,
            ],
            'data' => $result,
        ]);
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
