<?php

namespace App\Models;

use App\Enums\CitationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Citation extends Model
{
    use LogsActivity;

    protected $fillable = [
        'citation_number',
        'violation_type_id',
        'driver_id',
        'vehicle_id',
        'issued_by',
        'penalty_amount',
        'status',
        'location',
        'notes',
        'issued_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'penalty_amount' => 'decimal:2',
            'status' => CitationStatus::class,
            'issued_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function enforcer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(CitationEvidence::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPayable(): bool
    {
        return in_array($this->status, [
            CitationStatus::Issued,
            CitationStatus::Overdue,
            CitationStatus::Clamped,
        ], true);
    }

    public function isPaid(): bool
    {
        return $this->status === CitationStatus::Paid
            || $this->status === CitationStatus::Released;
    }

    public function getQRCode(): string
    {
        $url = route('citizen.citation.detail', $this, false);

        return QrCode::size(150)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($url);
    }

    public function getQRCodeUrl(): string
    {
        $data = "Citation: {$this->citation_number} | Vehicle: {$this->vehicle->plate_number} | Amount: ₱{$this->penalty_amount}";
        $encoded = urlencode($data);

        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$encoded}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

