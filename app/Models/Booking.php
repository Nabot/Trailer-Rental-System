<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'booking_number',
        'trailer_id',
        'customer_id',
        'created_by',
        'status',
        'start_date',
        'end_date',
        'pickup_time',
        'whatsapp_number',
        'total_days',
        'rate_per_day',
        'rental_cost',
        'required_deposit',
        'delivery_fee',
        'straps_fee',
        'damage_waiver_fee',
        'subtotal',
        'total_amount',
        'paid_amount',
        'balance',
        'notes',
        'cancellation_reason',
        'confirmed_at',
        'picked_up_at',
        'returned_at',
        'cancelled_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pickup_time' => 'datetime',
        'total_days' => 'integer',
        'rate_per_day' => 'decimal:2',
        'rental_cost' => 'decimal:2',
        'required_deposit' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'straps_fee' => 'decimal:2',
        'damage_waiver_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_number) {
                $booking->booking_number = static::generateBookingNumber();
            }
        });
    }

    public static function generateBookingNumber(): string
    {
        $year = now()->year;
        $lastBooking = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastBooking ? (int) Str::afterLast($lastBooking->booking_number, '-') + 1 : 1;

        return sprintf('TRL-%d-%04d', $year, $number);
    }

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function pickupInspection()
    {
        return $this->hasOne(Inspection::class)->where('type', 'pickup');
    }

    public function returnInspection()
    {
        return $this->hasOne(Inspection::class)->where('type', 'return');
    }

    public function calculateTotalDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1; // Inclusive
    }

    public function calculateRentalCost(): float
    {
        return $this->total_days * $this->rate_per_day;
    }

    public function calculateSubtotal(): float
    {
        return $this->rental_cost + $this->delivery_fee + $this->straps_fee + $this->damage_waiver_fee;
    }

    public function updateBalance(): void
    {
        $this->balance = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    public function canTransitionTo(string $status): bool
    {
        $transitions = [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['active', 'cancelled'],
            'active' => ['returned'],
            'returned' => [],
            'cancelled' => [],
        ];

        return in_array($status, $transitions[$this->status] ?? []);
    }

    public function transitionTo(string $status): bool
    {
        if (!$this->canTransitionTo($status)) {
            return false;
        }

        $this->status = $status;

        $timestampMap = [
            'confirmed' => 'confirmed_at',
            'active' => 'picked_up_at',
            'returned' => 'returned_at',
            'cancelled' => 'cancelled_at',
        ];

        if (isset($timestampMap[$status])) {
            $this->{$timestampMap[$status]} = now();
        }

        return $this->save();
    }
}
