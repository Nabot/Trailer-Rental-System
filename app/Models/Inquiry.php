<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inquiry_number',
        'source',
        'status',
        'priority',
        'customer_id',
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'preferred_start_date',
        'preferred_end_date',
        'trailer_interests',
        'rental_purpose',
        'budget_range',
        'notes',
        'assigned_to',
        'created_by',
        'converted_at',
        'converted_to_booking_id',
    ];

    protected $casts = [
        'preferred_start_date' => 'date',
        'preferred_end_date' => 'date',
        'trailer_interests' => 'array',
        'converted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (!$inquiry->inquiry_number) {
                $inquiry->inquiry_number = static::generateInquiryNumber();
            }
        });
    }

    public static function generateInquiryNumber(): string
    {
        $year = now()->year;
        $lastInquiry = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastInquiry ? (int) Str::afterLast($lastInquiry->inquiry_number, '-') + 1 : 1;

        return sprintf('INQ-%d-%04d', $year, $number);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedToBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'converted_to_booking_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(InquiryActivity::class)->orderBy('created_at', 'desc');
    }

    public function convertToBooking(Booking $booking): bool
    {
        $this->status = 'converted';
        $this->converted_at = now();
        $this->converted_to_booking_id = $booking->id;
        return $this->save();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'gray',
            'contacted' => 'blue',
            'quoted' => 'yellow',
            'follow_up' => 'orange',
            'converted' => 'green',
            'lost' => 'red',
            'on_hold' => 'purple',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }
}
