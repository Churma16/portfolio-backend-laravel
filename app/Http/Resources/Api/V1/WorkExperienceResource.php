<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'position' => $this->position,
            'location' => $this->location,
            'start_date' => $this->start_date->format("M Y"),
            'end_date' => $this->end_date->format("M Y"),
            'is_current' => $this->is_current,
            'description' => $this->description,
            'column_order' => $this->column_order,
            'achievements' => $this->achievements,
            'tags' => TagCollection::make($this->whenLoaded('tags')),
            'tech_stack' => TechStackCollection::make($this->whenLoaded('techStacks')),
        ];
    }
}
