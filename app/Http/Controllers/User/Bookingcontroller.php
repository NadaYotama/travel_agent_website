<?php

namespace App\Http\Controllers\User;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status = BookingStatus::tryFrom($request->string('status')->toString());
        $bookings = $request->user()->bookings()
            ->with('tourPackage:id,title,slug,start_date,end_date')
            ->when($status, fn ($query) => $query->where('status', $status->value))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('User.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'selectedStatus' => $status,
        ]);
    }

    /** Form pemesanan untuk sebuah paket. */
    public function create(TourPackage $package): View|RedirectResponse
    {
        $this->authorize('view', $package);

        if ($package->quota < 1 || ! $package->start_date || $package->start_date->isPast()) {
            return redirect()->route('user.packages.show', $package->slug)
                ->with('error', 'Paket ini tidak dapat dipesan.');
        }

        return view('User.bookings.create', compact('package'));
    }

    public function store(Request $request, TourPackage $package, CreateBookingAction $action): RedirectResponse
    {
        $this->authorize('view', $package);

        if (! $package->is_bookable) {
            return back()->withInput()->withErrors(['booking' => 'Paket ini sudah tidak dapat dipesan.']);
        }

        $data = $request->validate([
            'participants'             => ['required', 'array', 'min:1', 'max:20'],
            'participants.*.name'      => ['required', 'string', 'max:255'],
            'participants.*.id_number' => ['nullable', 'string', 'max:30'],
            'participants.*.phone'     => ['nullable', 'string', 'max:20'],
            'promo_code'               => ['nullable', 'string', 'max:30'],
            'notes'                    => ['nullable', 'string', 'max:1000'],
        ]);
        $data['total_participants'] = count($data['participants']);

        try {
            $booking = $action->execute($request->user(), $package, $data);
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['booking' => $e->getMessage()]);
        }

        return redirect()->route('user.payments.create', $booking->code)
            ->with('success', 'Booking berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['tourPackage.destination', 'participants', 'payments', 'promo']);

        return view('User.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $requiresRefund = $booking->payments()
            ->where('status', PaymentStatus::VERIFIED->value)
            ->exists();

        $data = $request->validate([
            'reason' => [$requiresRefund ? 'required' : 'nullable', 'string', 'max:500'],
        ]);

        try {
            $action->execute($booking, $data['reason'] ?? 'Dibatalkan oleh customer');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('user.bookings.show', $booking->code)
            ->with('success', $requiresRefund
                ? 'Booking dibatalkan. Permintaan refund telah dikirim dan menunggu diproses admin.'
                : 'Booking berhasil dibatalkan.');
    }
}