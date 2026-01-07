<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'headline',
        'role',
        'location',
        'bio_short',
        'bio_long',
        'is_hireable',
        'socials',
        'hero_image_codes',
        'avatar',
        'cv_files',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'socials' => 'array',
        ];
    }
}
