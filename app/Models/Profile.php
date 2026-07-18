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
        'cv_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], ['name' => 'Antonio Benalcázar']);
    }

    /**
     * The CV download is offered publicly only when a file exists and the
     * owner has it enabled from the admin panel.
     */
    public function cvAvailable(): bool
    {
        return (bool) $this->cv_path && (bool) ($this->cv_enabled ?? true);
    }
}
