<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Education extends Model
{
    use HasTranslations;

    protected $table = 'education';

    protected $guarded = [];

    public array $translatable = ['title', 'description'];
}
