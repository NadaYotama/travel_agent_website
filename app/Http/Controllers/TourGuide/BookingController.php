<?php

namespace App\Http\Controllers\TourGuide;

use App\Actions\Booking\CancelBookingAction;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransPaymentService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $scope = $request->input('scope', 'all'); // all | mine | unassigned

        $bookings = Booking::query()
            ->with([
                'user:id,name',
                'tourPackage:id,title,start_date',
                'agent:id,name',
                'payments:id,booking_id,status',
            ])
            ->when($scope === 'mine', fn ($q) => $q->where('agent_id', $request->user()->id))
            ->when($scope === 'unassigned', fn ($q) => $q->whereNull('agent_id'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(fn ($q) => $q
                    ->where('code', 'like', "%{$keyword}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$keyword}%")));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tourguide.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'scope'    => $scope,
        ]);
    }

    public function paymentStatuses(Request $request, MidtransPaymentService $midtrans): JsonResponse
    {
        $data = $request->validate([
            'booking_ids' => ['required', 'array', 'min:1', 'max:15'],
            'booking_ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $bookings = Booking::query()
            ->whereIn('id', $data['booking_ids'])
            ->get(['id', 'status']);

        if ($bookings->count() !== count($data['booking_ids'])) {
            abort(404);
        }

        foreach ($bookings as $booking) {
            $this->authorize('view', $booking);
        }

        $syncErrors = [];
        $payments = Payment::query()
            ->whereIn('booking_id', $bookings->pluck('id'))
            ->where('method', 'midtrans_snap')
            ->where('status', PaymentStatus::PENDING->value)
            ->get();

        foreach ($payments as $payment) {
            try {
                $midtrans->syncPendingPaymentStatus($payment);
            } catch (\Exception $exception) {
                $syncErrors[] = $payment->id;
                Log::warning('Unable to synchronize pending payment status with Midtrans.', [
                    'payment_id' => $payment->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $bookings = Booking::query()
            ->whereIn('id', $data['booking_ids'])
            ->withExists([
                'payments as has_verified_payment' => fn ($query) => $query
                    ->where('status', PaymentStatus::VERIFIED->value),
            ])
            ->get(['id', 'status']);

        return response()->json([
            'bookings' => $bookings->map(fn ($booking) => [
                'id' => $booking->id,
                'status' => $booking->status->value,
                'is_paid' => (bool) $booking->has_verified_payment,
            ])->values(),
            'sync_errors' => $syncErrors,
        ])->header('Cache-Control', 'private, no-store');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['user', 'agent:id,name', 'promo', 'tourPackage.destination', 'participants', 'payments']);

        return view('tourguide.bookings.show', compact('booking'));
    }

    /** Tour guide mengambil booking yang belum punya penanggung jawab. */
    public function claim(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('view', $booking);

        // Update atomik: hanya berhasil bila belum diambil tour guide lain.
        $claimed = Booking::whereKey($booking->id)
            ->whereNull('agent_id')
            ->update(['agent_id' => $request->user()->id]);

        return back()->with(
            $claimed ? 'success' : 'error',
            $claimed ? 'Booking berhasil Anda ambil.' : 'Booking ini sudah diambil tour guide lain.'
        );
    }

    public function paymentStatus(Booking $booking, MidtransPaymentService $midtrans): JsonResponse
    {
        $this->authorize('view', $booking);
        $booking->load('payments:id,booking_id,method,status,paid_at');
        $syncErrors = [];

        foreach ($booking->payments as $payment) {
            if ($payment->method !== 'midtrans_snap' || $payment->status !== PaymentStatus::PENDING) {
                continue;
            }

            try {
                $midtrans->syncPendingPaymentStatus($payment);
            } catch (\Exception $exception) {
                $syncErrors[] = $payment->id;
                Log::warning('Unable to synchronize pending payment status with Midtrans.', [
                    'payment_id' => $payment->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $booking->refresh()->load('payments:id,booking_id,method,status,paid_at');

        return response()->json([
            'booking_status' => $booking->status->value,
            'payments' => $booking->payments->map(fn ($payment) => [
                'id' => $payment->id,
                'method' => $payment->method,
                'status' => $payment->status->value,
                'paid_at' => $payment->paid_at?->toIso8601String(),
            ])->values(),
            'sync_errors' => $syncErrors,
        ])->header('Cache-Control', 'private, no-store');
    }

    /** Tandai perjalanan selesai (membuka akses ulasan untuk customer). */
    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('view', $booking);

        if ($booking->status !== BookingStatus::CONFIRMED) {
            return back()->with('error', 'Hanya booking terkonfirmasi yang dapat diselesaikan.');
        }

        if ($booking->tourPackage->end_date->isFuture()) {
            return back()->with('error', 'Perjalanan belum berakhir.');
        }

        $booking->update(['status' => BookingStatus::COMPLETED->value]);

        return back()->with('success', 'Booking ditandai selesai.');
    }

    public function cancel(Request $request, Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        try {
            $action->execute($booking, $data['reason'] ?? null);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Booking dibatalkan dan kuota paket dikembalikan.');
    }
}