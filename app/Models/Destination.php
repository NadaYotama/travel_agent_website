<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'country',
        'city',
        'description',
        'thumbnail',
    ];

    public function tourPackages(): HasMany
    {
        return $this->hasMany(TourPackage::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return Media::url($this->thumbnail);
    }
}
