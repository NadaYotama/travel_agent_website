<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function getUrlAttribute(): ?string
    {
        return Media::url($this->path);
    }
}
