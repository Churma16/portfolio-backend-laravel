<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechStackResource extends JsonResource
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
            'icon' => $this->icon,
            'category_id' => $this->category_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'projects' => ProjectCollection::make($this->whenLoaded('projects')),
            'workExperiences' => WorkExperienceCollection::make($this->whenLoaded('workExperiences')),

        ];
    }
}
