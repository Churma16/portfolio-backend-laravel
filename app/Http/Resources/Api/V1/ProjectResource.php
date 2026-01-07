<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail,
            'content' => $this->content,
            'demo_url' => $this->demo_url,
            'repo_url' => $this->repo_url,
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at,
            'tags' => TagCollection::make($this->whenLoaded('tags')),
            'tech_stack' => TechStackCollection::make($this->whenLoaded('techStacks')),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
