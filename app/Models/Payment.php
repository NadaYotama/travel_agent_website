<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Support\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'method',
        'amount',
        'status',
        'proof',
        'paid_at',
        'gateway_ref',
        'snap_token',
        'refund_status',
        'refund_reason',
        'refund_note',
        'refund_requested_at',
        'refund_processed_at',
    ];

    protected $casts = [
        'status'  => PaymentStatus::class,
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_processed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getProofUrlAttribute(): ?string
    {
        return Media::url($this->proof);
    }
}
