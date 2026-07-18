<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class SkillGroup extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name'];

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('sort');
    }
}
