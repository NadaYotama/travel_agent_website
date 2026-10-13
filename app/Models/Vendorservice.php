<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VendorService extends Model
{
    /** Jenis layanan yang dapat ditawarkan vendor. */
    public const TYPES = [
        'hotel'     => 'Hotel',
        'transport' => 'Transportasi',
        'guide'     => 'Pemandu wisata',
    ];

    protected $fillable = [
        'vendor_id',
        'type',
        'name',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function tourPackages(): BelongsToMany
    {
        return $this->belongsToMany(TourPackage::class, 'tour_package_vendor_service')
            ->withTimestamps();
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}