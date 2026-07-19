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

    /** Public lifecycle of the work itself (not publication status). */
    public const LIFECYCLES = [
        'production',
        'active_development',
        'internal_pilot',
        'implementation',
        'completed',
        'historical',
        'paused',
    ];

    public const CONFIDENTIALITY = ['public', 'confidential'];

    public array $translatable = [
        'name', 'summary', 'outcome_headline', 'description', 'problem', 'context',
        'constraints', 'solution', 'process', 'decisions', 'architecture_description',
        'result', 'improvements', 'role', 'responsibilities', 'learnings',
    ];

    protected $casts = [
        'seo' => 'array',
        'workflow_steps' => 'array',
        'features' => 'array',
        'technical_decisions' => 'array',
        'challenges' => 'array',
        'qualitative_results' => 'array',
        'external_links' => 'array',
        'is_featured' => 'boolean',
        'is_case_study' => 'boolean',
        'is_archived' => 'boolean',
        'is_ongoing' => 'boolean',
        'published_at' => 'datetime',
        'year' => 'integer',
        'completeness_score' => 'integer',
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

    public function visibleImages(): HasMany
    {
        return $this->images()->where('is_visible', true);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(ProjectMetric::class)->orderBy('sort');
    }

    public function publicMetrics(): HasMany
    {
        return $this->metrics()->where('is_public', true);
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

    public function scopeCaseStudies(Builder $query): Builder
    {
        return $query->where('is_case_study', true)->where('is_archived', false);
    }

    public function scopeArchive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('is_archived', true)->orWhere('is_case_study', false);
        });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && in_array($this->visibility, ['public', 'private_summary'], true)
            && (is_null($this->published_at) || $this->published_at->lte(now()));
    }

    public function isConfidential(): bool
    {
        return $this->confidentiality_level === 'confidential'
            || $this->visibility === 'private_summary';
    }

    public function translated(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $value = $this->getTranslation($field, $locale, false);

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        return null;
    }

    public function hasFilled(string $field, ?string $locale = null): bool
    {
        return filled($this->translated($field, $locale));
    }

    /** @return list<array{value?: string, unit?: string, label?: string, description?: string}> */
    public function orderedWorkflowSteps(): array
    {
        return collect($this->workflow_steps ?? [])
            ->filter(fn ($step) => filled(data_get($step, 'label.es') ?: data_get($step, 'label') ?: data_get($step, 'title')))
            ->values()
            ->all();
    }

    /** Completeness score 0–100 for admin only. */
    public function recalculateCompleteness(): int
    {
        $checks = [
            $this->hasFilled('name', 'es'),
            $this->hasFilled('summary', 'es'),
            filled($this->lifecycle),
            filled($this->main_image_path),
            $this->hasFilled('problem', 'es'),
            $this->hasFilled('responsibilities', 'es') || $this->hasFilled('role', 'es'),
            $this->hasFilled('solution', 'es'),
            $this->hasFilled('result', 'es') || $this->hasFilled('outcome_headline', 'es'),
            $this->metrics()->where('is_public', true)->exists(),
            $this->hasFilled('name', 'en') && $this->hasFilled('summary', 'en'),
            filled(data_get($this->seo, 'title.es')) || filled(data_get($this->seo, 'description.es')),
        ];

        $score = (int) round((collect($checks)->filter()->count() / max(count($checks), 1)) * 100);
        $this->completeness_score = $score;

        return $score;
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            $project->recalculateCompleteness();
        });
    }
}
