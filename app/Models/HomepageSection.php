<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class HomepageSection extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['title', 'subtitle', 'body'];

    protected $casts = [
        'data' => 'array',
        'is_visible' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)->orderBy('sort');
    }
}
