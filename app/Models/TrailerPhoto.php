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
        'url',
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

    /**
     * URL to use for the image (external URL or storage path).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! empty($this->url)) {
            return $this->url;
        }
        if (! empty($this->path)) {
            return storage_asset($this->path);
        }
        return null;
    }
}
