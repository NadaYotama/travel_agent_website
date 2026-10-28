<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourPackage;
use DomainException;
use Illuminate\Support\Facades\DB;

class CancelBookingAction
{
    public function execute(Booking $booking, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $reason): Booking {
            $package = TourPackage::query()
                ->whereKey($booking->tour_package_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedBooking->status, [
                BookingStatus::PENDING,
                BookingStatus::PAID,
                BookingStatus::CONFIRMED,
            ], true)) {
                throw new DomainException('Booking ini tidak dapat dibatalkan.');
            }

            $payments = Payment::query()
                ->where('booking_id', $lockedBooking->id)
                ->lockForUpdate()
                ->get();

            foreach ($payments as $payment) {
                if ($payment->status === PaymentStatus::VERIFIED) {
                    $payment->update([
                        'refund_status' => 'requested',
                        'refund_reason' => $reason ?: 'Pembatalan booking',
                        'refund_requested_at' => now(),
                    ]);
                } elseif ($payment->status === PaymentStatus::PENDING) {
                    $payment->update(['status' => PaymentStatus::REJECTED->value]);
                }
            }

            $lockedBooking->update(['status' => BookingStatus::CANCELLED->value]);
            $package->increment('quota', $lockedBooking->total_participants);

            return $lockedBooking->refresh();
        });
    }
}
