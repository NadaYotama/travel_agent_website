<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id',
        'title',
        'slug',
        'description',
        'price',
        'duration_days',
        'quota',
        'start_date',
        'end_date',
        'is_published',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'duration_days' => 'integer',
        'quota'         => 'integer',
        'start_date'    => 'date',
        'end_date'      => 'date',
        'is_published'  => 'boolean',
    ];

    // ---------- Relasi ----------

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class)->orderBy('day');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PackageImage::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Layanan vendor (hotel, transport, guide) yang dipakai paket ini. */
    public function vendorServices(): BelongsToMany
    {
        return $this->belongsToMany(VendorService::class, 'tour_package_vendor_service')
            ->withTimestamps();
    }

    // ---------- Scope ----------

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('start_date', '>=', today());
    }

    /** Terpublikasi, belum berangkat, dan masih ada kursi. */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->published()->upcoming()->where('quota', '>', 0);
    }

    // ---------- Helper ----------

    public function getIsBookableAttribute(): bool
    {
        return $this->is_published
            && $this->quota > 0
            && $this->start_date !== null
            && ! $this->start_date->isPast();
    }

    public function getCoverUrlAttribute(): ?string
    {
        $image = $this->relationLoaded('images') ? $this->images->first() : $this->images()->first();

        return $image ? Media::url($image->path) : null;
    }
}
