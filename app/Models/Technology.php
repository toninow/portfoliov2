<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technology extends Model
{
    protected $guarded = [];

    public const AREAS = ['backend', 'frontend', 'data', 'erp', 'infra', 'tools'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
