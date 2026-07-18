<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $guarded = [];

    public const STATUSES = ['draft', 'published', 'archived'];

    /** @var array<int, string> */
    public array $translatable = [
        'title', 'excerpt', 'body', 'topic',
    ];

    protected $casts = [
        'seo' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'reading_minutes' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && (is_null($this->published_at) || $this->published_at->isPast());
    }

    /**
     * Estimated reading time in minutes, computed from the body if not set.
     */
    public function readingMinutes(?string $locale = null): int
    {
        if ($this->reading_minutes) {
            return $this->reading_minutes;
        }

        $words = str_word_count(strip_tags((string) $this->getTranslation('body', $locale ?? app()->getLocale(), false)));

        return max(1, (int) ceil($words / 200));
    }
}
