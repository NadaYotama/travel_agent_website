<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PackageImage extends Model
{
    protected $fillable = [
        'tour_package_id',
        'path',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}