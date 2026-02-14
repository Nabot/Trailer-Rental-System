<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trailer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'axle',
        'size_m',
        'rate_per_day',
        'required_deposit',
        'status',
        'description',
        'registration_number',
        'colour',
        'load_capacity_kg',
        'trailer_value',
        'notes',
    ];

    protected $casts = [
        'size_m' => 'decimal:2',
        'rate_per_day' => 'decimal:2',
        'required_deposit' => 'decimal:2',
        'trailer_value' => 'decimal:2',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(TrailerPhoto::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TrailerDocument::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function primaryPhoto()
    {
        return $this->hasOne(TrailerPhoto::class)->where('is_primary', true);
    }

    public function isAvailableForDates($startDate, $endDate, $excludeBookingId = null): bool
    {
        $query = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0 && $this->status === 'available';
    }
}
