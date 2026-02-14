<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quote_number',
        'inquiry_id',
        'customer_id',
        'trailer_id',
        'start_date',
        'end_date',
        'total_days',
        'rate_per_day',
        'rental_cost',
        'delivery_fee',
        'straps_fee',
        'damage_waiver_fee',
        'subtotal',
        'tax',
        'total_amount',
        'validity_days',
        'expires_at',
        'status',
        'notes',
        'terms_conditions',
        'created_by',
        'sent_at',
        'accepted_at',
        'converted_to_booking_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'rate_per_day' => 'decimal:2',
        'rental_cost' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'straps_fee' => 'decimal:2',
        'damage_waiver_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'validity_days' => 'integer',
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (!$quote->quote_number) {
                $quote->quote_number = static::generateQuoteNumber();
            }
            if (!$quote->expires_at && $quote->validity_days) {
                $quote->expires_at = now()->addDays($quote->validity_days);
            }
        });

        static::saving(function ($quote) {
            if ($quote->total_days && $quote->rate_per_day) {
                $quote->rental_cost = $quote->total_days * $quote->rate_per_day;
            }
            $quote->subtotal = $quote->rental_cost + $quote->delivery_fee + $quote->straps_fee + $quote->damage_waiver_fee;
            $quote->total_amount = $quote->subtotal + $quote->tax;
        });
    }

    public static function generateQuoteNumber(): string
    {
        $year = now()->year;
        $lastQuote = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastQuote ? (int) Str::afterLast($lastQuote->quote_number, '-') + 1 : 1;

        return sprintf('QTE-%d-%04d', $year, $number);
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedToBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'converted_to_booking_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function calculateTotalDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsSent(): bool
    {
        $this->status = 'sent';
        $this->sent_at = now();
        return $this->save();
    }

    public function markAsAccepted(): bool
    {
        $this->status = 'accepted';
        $this->accepted_at = now();
        return $this->save();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'accepted' => 'green',
            'expired' => 'red',
            'converted' => 'purple',
            default => 'gray',
        };
    }
}
