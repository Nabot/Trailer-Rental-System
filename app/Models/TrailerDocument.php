<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrailerDocument extends Model
{
    protected $fillable = [
        'trailer_id',
        'type',
        'name',
        'path',
        'disk',
        'expiry_date',
        'reminder_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'reminder_date' => 'date',
    ];

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }

    public function isExpiringSoon($days = 30): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isBefore(now()->addDays($days)) && $this->expiry_date->isAfter(now());
    }
}
