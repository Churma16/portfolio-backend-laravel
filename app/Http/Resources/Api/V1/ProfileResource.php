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

        $avatar = "/storage/" . $this->avatar;
        $cv_files = "/storage/" . $this->cv_files;
        // $socials = json_encode($this->socials, JSON_UNESCAPED_SLASHES);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'headline' => $this->headline,
            'role' => $this->role,
            'bio_short' => $this->bio_short,
            'bio_long' => $this->bio_long,
            'location' => $this->location,
            'is_hireable' => $this->is_hireable,
            'avatar' => $avatar,
            'cv_files' => $cv_files,
            'hero_image_codes' => $this->hero_image_codes,
            'socials' => $this->socials,
        ];
    }
}
