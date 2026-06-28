<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TechStackCategoryCollection extends ResourceCollection
{
    public $collects = TechStackCategoryResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
