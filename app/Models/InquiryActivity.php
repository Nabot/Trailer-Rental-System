<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryActivity extends Model
{
    protected $fillable = [
        'inquiry_id',
        'type',
        'subject',
        'description',
        'scheduled_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function markAsCompleted(): bool
    {
        $this->completed_at = now();
        return $this->save();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_at && 
               $this->scheduled_at->isPast() && 
               !$this->isCompleted();
    }
}
