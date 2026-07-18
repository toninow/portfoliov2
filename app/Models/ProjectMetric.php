<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProjectMetric extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name', 'description'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
