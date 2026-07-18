<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasSlug;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['title', 'summary', 'problems', 'includes', 'deliverables', 'use_cases'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Service $s) => $s->getTranslation('title', 'es') ?: 'service')
            ->saveSlugsTo('slug');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('sort');
    }
}
