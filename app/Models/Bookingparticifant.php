<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingParticipant extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'id_number',
        'phone',
    ];

    /** Nomor identitas (KTP/paspor) disimpan terenkripsi. */
    protected $casts = [
        'id_number' => 'encrypted',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}