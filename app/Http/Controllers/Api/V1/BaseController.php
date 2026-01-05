<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

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

}
