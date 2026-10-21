<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;

class MidtransPaymentService
{
    public function isValidSignature(array $notification): bool
    {
        $serverKey = config('services.midtrans.server_key');
        if (! is_string($serverKey) || $serverKey === '') {
            return false;
        }

        $signature = hash(
            'sha512',
            $notification['order_id'] .
            $notification['status_code'] .
            $notification['gross_amount'] .
            $serverKey
        );

        return hash_equals($signature, $notification['signature_key']);
    }

    public function amountsMatch(string $expected, string $received): bool
    {
        $toMinorUnits = static function (string $amount): ?int {
            if (! preg_match('/^\d+(?:\.\d{1,2})?$/', $amount)) {
                return null;
            }

            [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');

            return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
        };

        $expectedAmount = $toMinorUnits($expected);
        $receivedAmount = $toMinorUnits($received);

        return $expectedAmount !== null
            && $receivedAmount !== null
            && $expectedAmount === $receivedAmount;
    }

    public function syncPendingPaymentStatus(Payment $payment): bool
    {
        if (
            $payment->method !== 'midtrans_snap'
            || $payment->status !== PaymentStatus::PENDING
            || ! $payment->gateway_ref
        ) {
            return false;
        }

        $cacheKey = 'midtrans-status-check:' . $payment->id;
        if (! Cache::add($cacheKey, true, now()->addSeconds(8))) {
            return false;
        }

        try {
            $serverKey = config('services.midtrans.server_key');
            if (! is_string($serverKey) || $serverKey === '') {
                throw new RuntimeException('Midtrans server key is not configured.');
            }

            Config::$serverKey = $serverKey;
            Config::$isProduction = (bool) config('services.midtrans.is_production');

            $result = Transaction::status($payment->gateway_ref);
            $status = is_object($result) ? get_object_vars($result) : $result;

            if (
                ! is_array($status)
                || ($status['order_id'] ?? null) !== $payment->gateway_ref
                || (string) ($status['status_code'] ?? '') !== '200'
                || ! isset($status['gross_amount'], $status['transaction_status'])
                || ! $this->amountsMatch((string) $payment->amount, (string) $status['gross_amount'])
            ) {
                throw new RuntimeException('Midtrans status response did not match the pending payment.');
            }

            $this->applyTransactionStatus(
                $payment,
                (string) $status['transaction_status'],
                isset($status['fraud_status']) ? (string) $status['fraud_status'] : null
            );

            return true;
        } catch (\Exception $exception) {
            Cache::forget($cacheKey);
            throw $exception;
        }
    }

    public function applyTransactionStatus(
        Payment $payment,
        string $transactionStatus,
        ?string $fraudStatus = null
    ): void {
        DB::transaction(function () use ($payment, $transactionStatus, $fraudStatus): void {
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($lockedPayment->booking_id)->lockForUpdate()->firstOrFail();
            $isSuccessful = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if ($isSuccessful) {
                if ($lockedPayment->status !== PaymentStatus::VERIFIED) {
                    $lockedPayment->update([
                        'status' => PaymentStatus::VERIFIED->value,
                        'paid_at' => now(),
                    ]);
                }

                if (in_array($booking->status, [BookingStatus::PENDING, BookingStatus::PAID], true)) {
                    $booking->update(['status' => BookingStatus::CONFIRMED->value]);
                } elseif ($booking->status === BookingStatus::CANCELLED) {
                    \Illuminate\Support\Facades\Log::critical(
                        'Midtrans settled a payment for a cancelled booking; manual reconciliation is required.',
                        ['booking_id' => $booking->id, 'payment_id' => $lockedPayment->id]
                    );
                }

                return;
            }

            if (
                in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)
                && $lockedPayment->status === PaymentStatus::PENDING
            ) {
                $lockedPayment->update(['status' => PaymentStatus::REJECTED->value]);
            }
        });
    }

    public function createSnapToken(Booking $booking): string
    {
        $serverKey = config('services.midtrans.server_key');
        $clientKey = config('services.midtrans.client_key');

        if (! is_string($serverKey) || $serverKey === '' || ! is_string($clientKey) || $clientKey === '') {
            throw new RuntimeException('Midtrans credentials are not configured.');
        }

        return DB::transaction(function () use ($booking, $serverKey, $clientKey): string {
            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status !== BookingStatus::PENDING) {
                throw new \DomainException('Pesanan ini tidak lagi menunggu pembayaran.');
            }

            $payment = $lockedBooking->payments()
                ->where('method', 'midtrans_snap')
                ->where('status', PaymentStatus::PENDING->value)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($payment?->snap_token) {
                return $payment->snap_token;
            }

            $lockedBooking->loadMissing(['user', 'tourPackage']);

            $amount = (float) $lockedBooking->total_price;
            if ($amount <= 0 || floor($amount) !== $amount) {
                throw new RuntimeException('Booking total must be a positive whole amount in IDR.');
            }

            if (! $payment) {
                $payment = $lockedBooking->payments()->create([
                    'method' => 'midtrans_snap',
                    'amount' => $lockedBooking->total_price,
                    'status' => PaymentStatus::PENDING->value,
                ]);
            }

            $orderId = 'BOOK-' . $lockedBooking->id . '-PAY-' . $payment->id;
            $payment->update(['gateway_ref' => $orderId]);

            $this->configureSnap($serverKey, $clientKey);
            Config::$overrideNotifUrl = route('api.midtrans.notifications');

            $customer = [
                'first_name' => $lockedBooking->user->name,
                'email' => $lockedBooking->user->email,
            ];
            if ($lockedBooking->user->phone) {
                $customer['phone'] = $lockedBooking->user->phone;
            }

            $token = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $amount,
                ],
                'customer_details' => $customer,
            ]);

            if (! is_string($token) || $token === '') {
                throw new RuntimeException('Midtrans returned an empty Snap token.');
            }

            $payment->update(['snap_token' => $token]);

            return $token;
        });
    }

    private function configureSnap(string $serverKey, string $clientKey): void
    {
        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}
