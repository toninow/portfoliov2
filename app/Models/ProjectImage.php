<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProjectImage extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['alt', 'caption'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
