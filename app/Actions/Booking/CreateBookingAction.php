<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateBookingAction
{
    public function execute(User $user, TourPackage $package, array $data): Booking
    {
        return DB::transaction(function () use ($user, $package, $data) {
            $package = TourPackage::lockForUpdate()->findOrFail($package->id);

            if ($package->quota < $data['total_participants']) {
                throw new \DomainException('Kuota paket tidak mencukupi.');
            }

            $package->decrement('quota', $data['total_participants']);

            $booking = Booking::create([
                'code'               => 'BKG-' . strtoupper(Str::random(8)),
                'user_id'            => $user->id,
                'tour_package_id'    => $package->id,
                'total_participants' => $data['total_participants'],
                'total_price'        => $package->price * $data['total_participants'],
                'status'             => BookingStatus::PENDING,
                'notes'              => $data['notes'] ?? null,
            ]);

            $booking->participants()->createMany($data['participants']);

            return $booking;
        });
    }
}