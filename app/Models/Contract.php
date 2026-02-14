<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Contract extends Model
{
    protected $fillable = [
        'contract_number',
        'booking_id',
        'customer_id',
        'contract_date',
        'status',
        'signed_at',
        'customer_signature_path',
        'terms_conditions',
        'notes',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'signed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (!$contract->contract_number) {
                $contract->contract_number = static::generateContractNumber();
            }
        });
    }

    public static function generateContractNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $number = $last ? (int) Str::afterLast($last->contract_number, '-') + 1 : 1;
        return sprintf('CNT-%d-%04d', $year, $number);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }
}
