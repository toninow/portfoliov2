<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Profile extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['headline', 'bio', 'about_long', 'availability', 'degree'];

    protected $casts = [
        'extras' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], ['name' => 'Antonio Benalcázar']);
    }
}
