<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tags' => TagCollection::make($this->whenLoaded('tags')),
            'tech_stack' => TechStackCollection::make($this->whenLoaded('techStacks')),
        ];
    }
}
