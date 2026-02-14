<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    protected $fillable = [
        'booking_id',
        'type',
        'checklist',
        'notes',
        'condition_notes',
        'is_damaged',
        'total_damage_cost',
        'inspected_by',
        'inspected_at',
    ];

    protected $casts = [
        'checklist' => 'array',
        'is_damaged' => 'boolean',
        'total_damage_cost' => 'decimal:2',
        'inspected_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class);
    }

    public function damageItems(): HasMany
    {
        return $this->hasMany(DamageItem::class);
    }

    public function updateTotalDamageCost(): void
    {
        $this->total_damage_cost = $this->damageItems()->sum('estimated_cost');
        $this->save();
    }
}
