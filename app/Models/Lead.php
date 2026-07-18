<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $guarded = [];

    public const STATUSES = [
        'new', 'contacted', 'conversation', 'proposal_sent', 'won', 'lost', 'archived',
    ];

    protected $casts = [
        'next_follow_up_at' => 'datetime',
        'contacted_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('happened_at');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logActivity(string $type, ?string $description = null, array $meta = []): LeadActivity
    {
        return $this->activities()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
            'happened_at' => now(),
        ]);
    }
}
