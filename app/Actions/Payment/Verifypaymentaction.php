<?php

namespace App\Actions\Payment;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Menyetujui / menolak pembayaran manual (bukti transfer).
 * Dipakai bersama oleh Admin dan Agent.
 *
 * Alur status booking:
 *   user upload bukti  -> booking PAID      (menunggu verifikasi)
 *   disetujui          -> booking CONFIRMED
 *   ditolak            -> booking PENDING   (user bisa upload ulang)
 *
 * @throws DomainException
 */
class VerifyPaymentAction
{
    public function execute(Payment $payment, bool $approve): Payment
    {
        return DB::transaction(function () use ($payment, $approve) {
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);
            $booking = $payment->booking;

            if ($payment->status !== PaymentStatus::PENDING) {
                throw new DomainException('Pembayaran ini sudah diproses sebelumnya.');
            }

            if ($booking->status === BookingStatus::CANCELLED) {
                throw new DomainException('Booking sudah dibatalkan, pembayaran tidak dapat diproses.');
            }

            if ($approve) {
                $payment->update([
                    'status'  => PaymentStatus::VERIFIED->value,
                    'paid_at' => $payment->paid_at ?? now(),
                ]);
                $booking->update(['status' => BookingStatus::CONFIRMED->value]);
            } else {
                $payment->update(['status' => PaymentStatus::REJECTED->value]);

                if ($booking->status === BookingStatus::PAID) {
                    $booking->update(['status' => BookingStatus::PENDING->value]);
                }
            }

            // Kirim notifikasi ke customer (aktifkan setelah Notification dibuat):
            // DB::afterCommit(fn () => $booking->user->notify(new \App\Notifications\PaymentConfirmed($payment)));

            return $payment;
        });
    }
}