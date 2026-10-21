<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\MidtransPaymentService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/** Pembayaran pelanggan melalui Midtrans Snap. */
class PaymentController extends Controller
{
    public function create(Booking $booking): View
    {
        $this->authorize('pay', $booking);

        $booking->load('tourPackage:id,title,slug');

        return view('User.payments.create', [
            'booking'           => $booking,
            'midtransAvailable' => filled(config('services.midtrans.client_key'))
                && filled(config('services.midtrans.server_key')),
            'midtransClientKey' => config('services.midtrans.client_key'),
            'midtransProduction' => config('services.midtrans.is_production'),
        ]);
    }

    public function createMidtransPayment(
        Booking $booking,
        MidtransPaymentService $midtrans
    ): JsonResponse {
        $this->authorize('pay', $booking);

        try {
            $snapToken = $midtrans->createSnapToken($booking);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap transaction could not be created.', [
                'booking_id' => $booking->id,
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'Midtrans tidak dapat menyiapkan pembayaran saat ini. Silakan coba kembali.',
            ], 503);
        }

        return response()->json(['snap_token' => $snapToken]);
    }
}