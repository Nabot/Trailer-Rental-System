<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DamageItem extends Model
{
    protected $fillable = [
        'inspection_id',
        'description',
        'location',
        'estimated_cost',
        'severity',
        'repaired',
        'repaired_at',
        'repair_notes',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'repaired' => 'boolean',
        'repaired_at' => 'date',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class);
    }
}
