<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrailerPhoto extends Model
{
    protected $fillable = [
        'trailer_id',
        'path',
        'disk',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer',
    ];

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }
}
