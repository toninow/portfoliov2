<?php

namespace App\Models;

use App\Support\Locale;
use App\Support\TechnologyTaxonomy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Translatable\HasTranslations;

class Technology extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['description'];

    public const AREAS = [
        'backend',
        'data',
        'web',
        'platforms',
        'infra',
        'tools',
        'additional',
    ];

    public const RELEVANCES = ['primary', 'practical', 'previous'];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_about' => 'boolean',
        'show_on_projects' => 'boolean',
        'last_used_on' => 'date',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOnAbout(Builder $query): Builder
    {
        return $query->where('show_on_about', true);
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('relevance', 'primary');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort')->orderBy('name');
    }

    public function areaLabel(?string $locale = null): string
    {
        return TechnologyTaxonomy::areaLabel($this->area, $locale);
    }

    public function relevanceLabel(?string $locale = null): string
    {
        return TechnologyTaxonomy::relevanceLabel($this->relevance ?: 'practical', $locale);
    }

    public function projectsIndexUrl(): string
    {
        return Locale::route('projects.index', ['tecnologia' => $this->slug]);
    }

    /** @return Collection<int, Project> */
    public function relatedPublicProjects(int $limit = 2)
    {
        return $this->projects
            ->filter(fn (Project $project) => $project->status === 'published'
                && in_array($project->visibility, ['public', 'private_summary'], true))
            ->sortBy([
                ['is_featured', 'desc'],
                ['sort', 'asc'],
            ])
            ->take($limit)
            ->values();
    }
}
