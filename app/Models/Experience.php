<?php

namespace App\Models;

use App\Support\ExperienceModality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['role', 'description'];

    protected $casts = [
        'is_current' => 'boolean',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'tech_tags' => 'array',
        'achievements' => 'array',
    ];

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('start_date');
    }

    public function displayLocation(): ?string
    {
        $parts = array_filter([$this->city, $this->country ?: $this->location]);

        if ($parts === []) {
            return null;
        }

        // Avoid "Ecuador · Ecuador" when country and legacy location match.
        $parts = array_values(array_unique($parts));

        return implode(', ', $parts);
    }

    public function modalityLabel(?string $locale = null): ?string
    {
        return ExperienceModality::label($this->modality, $locale);
    }

    /**
     * Human-readable period for the public site.
     * Uses year strings already stored (no invented months).
     */
    public function periodLabel(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $start = trim((string) $this->start_date);
        $end = trim((string) $this->end_date);
        $present = $locale === 'es' ? 'Actualidad' : 'Present';

        if ($start === '' && $end === '' && ! $this->is_current) {
            return '';
        }

        if ($this->is_current) {
            return $start !== '' ? $start.' – '.$present : $present;
        }

        if ($start !== '' && $end !== '') {
            return $start.' – '.$end;
        }

        return $start !== '' ? $start : $end;
    }

    public function startDateTimeAttr(): ?string
    {
        return $this->yearAttr($this->start_date);
    }

    public function endDateTimeAttr(): ?string
    {
        if ($this->is_current) {
            return null;
        }

        return $this->yearAttr($this->end_date);
    }

    /** @return list<array{title: string, description?: string, metric?: string}> */
    public function publicAchievements(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $items = is_array($this->achievements) ? $this->achievements : [];

        return collect($items)
            ->filter(function ($item) {
                if (! is_array($item)) {
                    return false;
                }
                if (array_key_exists('is_public', $item) && ! $item['is_public']) {
                    return false;
                }

                return filled($item['title'] ?? null);
            })
            ->sortBy(fn ($item) => $item['sort'] ?? 0)
            ->map(function (array $item) use ($locale) {
                $title = $item['title'] ?? '';
                if (is_array($title)) {
                    $title = $title[$locale] ?? $title['es'] ?? reset($title);
                }
                $description = $item['description'] ?? '';
                if (is_array($description)) {
                    $description = $description[$locale] ?? $description['es'] ?? reset($description);
                }

                return array_filter([
                    'title' => (string) $title,
                    'description' => filled($description) ? (string) $description : null,
                    'metric' => filled($item['metric'] ?? null) ? (string) $item['metric'] : null,
                ]);
            })
            ->values()
            ->all();
    }

    /** @return list<string> */
    public function publicTechTags(): array
    {
        return collect($this->tech_tags ?? [])
            ->filter(fn ($tag) => filled($tag))
            ->map(fn ($tag) => (string) $tag)
            ->unique()
            ->values()
            ->all();
    }

    protected function yearAttr(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        if (preg_match('/^(\d{4})/', trim($value), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
