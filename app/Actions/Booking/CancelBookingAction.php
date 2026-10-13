<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Promo;
use App\Models\TourPackage;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Membuat booking baru untuk seorang customer.
 *
 * Format $data:
 * [
 *   'participants' => [
 *       ['name' => 'Budi', 'id_number' => '3201...', 'phone' => '0812...'],
 *       ...
 *   ],
 *   'promo_code'   => 'HEMAT10',   // opsional
 *   'notes'        => 'Kamar non-smoking', // opsional
 * ]
 *
 * Jumlah peserta dihitung dari panjang array 'participants', sehingga tidak
 * mungkin berbeda dengan data peserta yang tersimpan.
 *
 * @throws DomainException jika paket/promo tidak valid atau kuota habis
 */
class CreateBookingAction
{
    public function execute(User $user, TourPackage $package, array $data): Booking
    {
        $participants = array_values($data['participants'] ?? []);
        $totalParticipants = count($participants);

        if ($totalParticipants < 1) {
            throw new DomainException('Minimal harus ada satu peserta.');
        }

        return DB::transaction(function () use ($user, $package, $data, $participants, $totalParticipants) {
            // Kunci baris paket agar dua booking bersamaan tidak melewati batas kuota.
            $package = TourPackage::lockForUpdate()->findOrFail($package->id);

            $this->ensurePackageBookable($package, $totalParticipants);

            $subtotal = round((float) $package->price * $totalParticipants, 2);

            $promo    = null;
            $discount = 0.0;

            if (! empty($data['promo_code'])) {
                $promo    = $this->resolvePromo($data['promo_code']);
                $discount = $this->calculateDiscount($promo, $subtotal);
            }

            $booking = Booking::create([
                'code'               => $this->generateCode(),
                'user_id'            => $user->id,
                'tour_package_id'    => $package->id,
                'promo_id'           => $promo?->id,
                'total_participants' => $totalParticipants,
                'total_price'        => $subtotal - $discount,
                'status'             => BookingStatus::PENDING->value,
                'notes'              => $data['notes'] ?? null,
            ]);

            $booking->participants()->createMany(
                array_map(fn (array $p) => [
                    'name'      => $p['name'],
                    'id_number' => $p['id_number'] ?? null,
                    'phone'     => $p['phone'] ?? null,
                ], $participants)
            );

            $package->decrement('quota', $totalParticipants);

            // Kirim notifikasi setelah transaksi berhasil (aktifkan bila class sudah dibuat):
            // DB::afterCommit(fn () => $user->notify(new \App\Notifications\BookingCreated($booking)));

            return $booking->load(['tourPackage', 'participants', 'promo']);
        });
    }

    private function ensurePackageBookable(TourPackage $package, int $participants): void
    {
        if (! $package->is_published) {
            throw new DomainException('Paket ini sedang tidak tersedia.');
        }

        if ($package->start_date && $package->start_date->isPast()) {
            throw new DomainException('Paket ini sudah berangkat dan tidak dapat dipesan.');
        }

        if ($package->quota < $participants) {
            throw new DomainException(
                $package->quota > 0
                    ? "Kuota tersisa {$package->quota} kursi, tidak cukup untuk {$participants} peserta."
                    : 'Kuota paket sudah habis.'
            );
        }
    }

    private function resolvePromo(string $code): Promo
    {
        // Kunci baris promo agar pemakaian kuota promo tidak terlampaui saat ada booking bersamaan.
        $promo = Promo::lockForUpdate()
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (! $promo) {
            throw new DomainException('Kode promo tidak ditemukan.');
        }

        if ($promo->valid_until->endOfDay()->isPast()) {
            throw new DomainException('Kode promo sudah kedaluwarsa.');
        }

        // Pemakaian dihitung dari booking yang tidak dibatalkan,
        // jadi pembatalan otomatis "mengembalikan" kuota promo.
        $used = $promo->bookings()
            ->where('status', '!=', BookingStatus::CANCELLED->value)
            ->count();

        if ($used >= $promo->quota) {
            throw new DomainException('Kuota kode promo sudah habis.');
        }

        return $promo;
    }

    private function calculateDiscount(Promo $promo, float $subtotal): float
    {
        $discount = $promo->discount_type === 'percent'
            ? $subtotal * ((float) $promo->discount_value / 100)
            : (float) $promo->discount_value;

        // Diskon tidak boleh melebihi total harga.
        return round(min($discount, $subtotal), 2);
    }

    private function generateCode(): string
    {
        do {
            $code = 'BKG-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (Booking::withTrashed()->where('code', $code)->exists());

        return $code;
    }
}