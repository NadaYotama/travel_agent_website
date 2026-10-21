<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransPaymentService $midtrans): JsonResponse
    {
        $notification = $request->validate([
            'order_id' => ['required', 'string', 'max:50'],
            'status_code' => ['required'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'signature_key' => ['required', 'string', 'size:128'],
            'transaction_status' => ['required', 'string', 'max:30'],
            'fraud_status' => ['nullable', 'string', 'max:30'],
        ]);

        $notification['status_code'] = (string) $notification['status_code'];
        $notification['gross_amount'] = (string) $notification['gross_amount'];

        if (! $midtrans->isValidSignature($notification)) {
            return response()->json(['message' => 'Invalid notification signature.'], 403);
        }

        $payment = Payment::query()
            ->where('method', 'midtrans_snap')
            ->where('gateway_ref', $notification['order_id'])
            ->first();

        if (! $payment) {
            Log::warning('Midtrans notification references an unknown order.', [
                'order_id' => $notification['order_id'],
            ]);

            return response()->json(['message' => 'Payment order not found.'], 404);
        }

        if (! $midtrans->amountsMatch((string) $payment->amount, $notification['gross_amount'])) {
            Log::warning('Midtrans notification amount does not match the payment.', [
                'payment_id' => $payment->id,
            ]);

            return response()->json(['message' => 'Notification amount does not match.'], 422);
        }

        $midtrans->applyTransactionStatus(
            $payment,
            $notification['transaction_status'],
            $notification['fraud_status'] ?? null
        );

        return response()->json(['message' => 'Notification processed.']);
    }
}
