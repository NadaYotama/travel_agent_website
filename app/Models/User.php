<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
// use Laravel\Sanctum\HasApiTokens; // aktifkan bila nanti membuat API mobile

class User extends Authenticatable // implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Catatan: password TIDAK memakai cast "hashed". Seluruh controller
     * (dan Breeze) sudah memanggil Hash::make() secara eksplisit, sehingga
     * cast tambahan akan membuat password ter-hash dua kali.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role'              => UserRole::class,
        'is_active'         => 'boolean',
    ];

    // ---------- Relasi ----------

    /** Booking yang dibuat user ini sebagai customer. */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Booking yang ditangani user ini sebagai agent. */
    public function handledBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'agent_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    // ---------- Helper ----------

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }
}