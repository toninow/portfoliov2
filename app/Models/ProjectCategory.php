<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ProjectCategory extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name', 'description'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
