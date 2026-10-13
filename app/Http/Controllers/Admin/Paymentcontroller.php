<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Daftar pembayaran. Tambahkan route:
     * Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
     */
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
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
        ]);

        if ($payment->status !== PaymentStatus::PENDING) {
            return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $booking = $payment->booking;

        if ($data['decision'] === 'approve' && $booking->status === BookingStatus::CANCELLED) {
            return back()->with('error', 'Booking sudah dibatalkan, pembayaran tidak dapat disetujui.');
        }

        DB::transaction(function () use ($payment, $booking, $data) {
            if ($data['decision'] === 'approve') {
                $payment->update([
                    'status'  => PaymentStatus::VERIFIED->value,
                    'paid_at' => $payment->paid_at ?? now(),
                ]);

                $booking->update(['status' => BookingStatus::CONFIRMED->value]);
            } else {
                $payment->update(['status' => PaymentStatus::REJECTED->value]);
            }
        });

        // Kirim notifikasi ke customer (aktifkan setelah Notification dibuat):
        // $booking->user->notify(new \App\Notifications\PaymentConfirmed($payment));

        return back()->with(
            'success',
            $data['decision'] === 'approve'
                ? 'Pembayaran disetujui dan booking dikonfirmasi.'
                : 'Pembayaran ditolak.'
        );
    }
}