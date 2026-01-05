<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'cv_files' => $this->cv_files,
            'socials' => $this->socials,
        ];
    }
}
