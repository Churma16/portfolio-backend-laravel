<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'content',
        'demo_url',
        'repo_url',
        'is_featured',
        'published_at',
        'category_id',
        'column_order',
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
            'is_featured' => 'boolean',
            'published_at' => 'timestamp',
            'column_order' => 'integer',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->column_order = (static::max('column_order') ?? 0) + 1;
        });
    }

    public function techStacks(): BelongsToMany
    {
        return $this->belongsToMany(TechStack::class)->orderBy('column_order', 'asc');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
