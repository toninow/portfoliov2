<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NavigationItem extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['label'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}
