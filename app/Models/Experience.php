<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['role', 'description'];

    protected $casts = [
        'is_current' => 'boolean',
    ];
}
