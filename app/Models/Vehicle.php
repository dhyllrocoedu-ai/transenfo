<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'owner_id',
        'driver_id',
        'plate_number',
        'classification',
        'make',
        'model',
        'color',
        'year',
        'registration_status',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function citations(): HasMany
    {
        return $this->hasMany(Citation::class);
    }

    public function clampingRecords(): HasMany
    {
        return $this->hasMany(ClampingRecord::class);
    }

    public function activeClamp(): ?ClampingRecord
    {
        return $this->clampingRecords()
            ->where('status', 'active')
            ->latest('clamped_at')
            ->first();
    }

    public function hasUnpaidCitations(): bool
    {
        return $this->citations()
            ->whereIn('status', ['issued', 'overdue', 'clamped'])
            ->exists();
    }
}
