<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $guarded = [];

    public const STATUSES = ['draft', 'published', 'archived'];

    public const VISIBILITIES = ['public', 'private_summary', 'draft', 'archived'];

    public array $translatable = [
        'name', 'summary', 'description', 'problem', 'context', 'constraints',
        'solution', 'process', 'decisions', 'result', 'improvements', 'role',
    ];

    protected $casts = [
        'seo' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'year' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(ProjectMetric::class)->orderBy('sort');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereIn('visibility', ['public', 'private_summary'])
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && in_array($this->visibility, ['public', 'private_summary'], true)
            && (is_null($this->published_at) || $this->published_at->isPast());
    }
}
