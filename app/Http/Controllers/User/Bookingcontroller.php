<?php

namespace App\Http\Controllers\User;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Enums\BookingStatus;
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
        $bookings = $request->user()->bookings()
            ->with('tourPackage:id,title,slug,start_date')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    /** Form pemesanan untuk sebuah paket. */
    public function create(TourPackage $package): View|RedirectResponse
    {
        $this->authorize('view', $package);

        if ($package->quota < 1 || $package->start_date->isPast()) {
            return redirect()->route('user.packages.show', $package->slug)
                ->with('error', 'Paket ini tidak dapat dipesan.');
        }

        return view('user.bookings.create', compact('package'));
    }

    public function store(Request $request, TourPackage $package, CreateBookingAction $action): RedirectResponse
    {
        $this->authorize('view', $package);

        $data = $request->validate([
            'participants'             => ['required', 'array', 'min:1', 'max:20'],
            'participants.*.name'      => ['required', 'string', 'max:255'],
            'participants.*.id_number' => ['nullable', 'string', 'max:30'],
            'participants.*.phone'     => ['nullable', 'string', 'max:20'],
            'promo_code'               => ['nullable', 'string', 'max:30'],
            'notes'                    => ['nullable', 'string', 'max:1000'],
        ]);

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

        return view('user.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        try {
            $action->execute($booking, 'Dibatalkan oleh customer');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('user.bookings.show', $booking->code)
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}