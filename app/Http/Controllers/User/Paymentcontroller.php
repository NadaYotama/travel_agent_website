<?php

namespace App\Http\Controllers\User;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Pembayaran manual (upload bukti transfer).
 * Untuk gateway otomatis (Midtrans/Xendit), tambahkan method di PaymentGatewayService
 * dan webhook di routes/api.php.
 */
class PaymentController extends Controller
{
    public function create(Booking $booking): View
    {
        $this->authorize('pay', $booking);

        $booking->load('tourPackage:id,title');

        return view('user.payments.create', [
            'booking'      => $booking,
            'bankAccounts' => config('travel.bank_accounts', []),
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('pay', $booking);

        $data = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = $data['proof']->store('payments/proofs', 'public');

        try {
            DB::transaction(function () use ($booking, $path) {
                // Kunci booking agar dua unggahan bersamaan tidak membuat dua pembayaran.
                $booking = Booking::lockForUpdate()->findOrFail($booking->id);

                if ($booking->status !== BookingStatus::PENDING) {
                    abort(409, 'Booking ini sudah tidak menunggu pembayaran.');
                }

                $booking->payments()->create([
                    'method' => 'bank_transfer',
                    'amount' => $booking->total_price,
                    'status' => PaymentStatus::PENDING->value,
                    'proof'  => $path,
                ]);

                $booking->update(['status' => BookingStatus::PAID->value]);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }

        return redirect()->route('user.bookings.show', $booking->code)
            ->with('success', 'Bukti pembayaran terkirim. Agent kami akan memverifikasinya.');
    }
}