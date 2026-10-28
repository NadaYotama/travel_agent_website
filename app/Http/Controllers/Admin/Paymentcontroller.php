<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /** Daftar pembayaran dan permintaan refund. */
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['booking:id,code,user_id,total_price', 'booking.user:id,name'])
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->string('status')->toString()),
                // Default: tampilkan yang menunggu verifikasi lebih dulu
                fn ($q) => $q->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
        ]);
    }

    /** Setujui atau tolak pembayaran (bukti transfer). */
    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->method === 'midtrans_snap') {
            return back()->with('error', 'Pembayaran Midtrans hanya dapat diperbarui melalui notifikasi resmi Midtrans.');
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
        ]);

        $result = DB::transaction(function () use ($payment, $data): ?string {
            $booking = Booking::query()->whereKey($payment->booking_id)->lockForUpdate()->firstOrFail();
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayment->status !== PaymentStatus::PENDING) {
                return 'processed';
            }

            if ($data['decision'] === 'approve' && $booking->status === BookingStatus::CANCELLED) {
                return 'cancelled';
            }

            if ($data['decision'] === 'approve') {
                $lockedPayment->update([
                    'status' => PaymentStatus::VERIFIED->value,
                    'paid_at' => $lockedPayment->paid_at ?? now(),
                ]);

                $booking->update(['status' => BookingStatus::CONFIRMED->value]);
            } else {
                $lockedPayment->update(['status' => PaymentStatus::REJECTED->value]);
            }

            return null;
        });

        if ($result === 'processed') {
            return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        if ($result === 'cancelled') {
            return back()->with('error', 'Booking sudah dibatalkan, pembayaran tidak dapat disetujui.');
        }

        // Kirim notifikasi ke customer (aktifkan setelah Notification dibuat):
        // $booking->user->notify(new \App\Notifications\PaymentConfirmed($payment));

        return back()->with(
            'success',
            $data['decision'] === 'approve'
                ? 'Pembayaran disetujui dan booking dikonfirmasi.'
                : 'Pembayaran ditolak.'
        );
    }

    /** Catat hasil pemrosesan refund manual setelah dana dikembalikan di luar aplikasi. */
    public function updateRefund(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in([RefundStatus::PROCESSED->value, RefundStatus::REJECTED->value])],
            'refund_note' => ['required', 'string', 'max:1000'],
        ]);

        $updated = DB::transaction(function () use ($payment, $data): bool {
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayment->refund_status !== RefundStatus::REQUESTED->value) {
                return false;
            }

            $lockedPayment->update([
                'refund_status' => $data['decision'],
                'refund_note' => $data['refund_note'],
                'refund_processed_at' => $data['decision'] === RefundStatus::PROCESSED->value ? now() : null,
            ]);

            return true;
        });

        if (! $updated) {
            return back()->with('error', 'Permintaan refund ini sudah diproses sebelumnya.');
        }

        return back()->with(
            'success',
            $data['decision'] === RefundStatus::PROCESSED->value
                ? 'Refund ditandai telah diproses.'
                : 'Permintaan refund ditolak.'
        );
    }
}