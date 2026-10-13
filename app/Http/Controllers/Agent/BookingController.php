<?php

namespace App\Http\Controllers\Agent;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Payment\VerifyPaymentAction;
use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $scope = $request->input('scope', 'all'); // all | mine | unassigned

        $bookings = Booking::query()
            ->with(['user:id,name', 'tourPackage:id,title,start_date', 'agent:id,name'])
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

        return view('agent.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'scope'    => $scope,
        ]);
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['user', 'agent:id,name', 'promo', 'tourPackage.destination', 'participants', 'payments']);

        return view('agent.bookings.show', compact('booking'));
    }

    /** Agent mengambil booking yang belum punya penanggung jawab. */
    public function claim(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('view', $booking);

        // Update atomik: hanya berhasil bila belum diambil agent lain.
        $claimed = Booking::whereKey($booking->id)
            ->whereNull('agent_id')
            ->update(['agent_id' => $request->user()->id]);

        return back()->with(
            $claimed ? 'success' : 'error',
            $claimed ? 'Booking berhasil Anda ambil.' : 'Booking ini sudah diambil agent lain.'
        );
    }

    /** Setujui atau tolak bukti pembayaran. */
    public function verifyPayment(Request $request, Payment $payment, VerifyPaymentAction $action): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
        ]);

        try {
            $action->execute($payment, $data['decision'] === 'approve');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Agent yang memproses otomatis menjadi penanggung jawab bila belum ada.
        Booking::whereKey($payment->booking_id)
            ->whereNull('agent_id')
            ->update(['agent_id' => $request->user()->id]);

        return back()->with(
            'success',
            $data['decision'] === 'approve'
                ? 'Pembayaran disetujui dan booking dikonfirmasi.'
                : 'Pembayaran ditolak. Customer dapat mengunggah bukti baru.'
        );
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