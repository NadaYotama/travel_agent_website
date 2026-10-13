<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED   = 'fixed';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'valid_until',
        'quota',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_until'    => 'date',
        'quota'          => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isExpired(): bool
    {
        return $this->valid_until->endOfDay()->isPast();
    }
}