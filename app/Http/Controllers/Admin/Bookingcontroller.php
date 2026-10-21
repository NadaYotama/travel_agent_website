<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user:id,name', 'tourPackage:id,title', 'agent:id,name'])
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

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'agent:id,name',
            'promo',
            'tourPackage.destination',
            'participants',
            'payments',
        ]);

        return view('admin.bookings.show', [
            'booking' => $booking,
            'statuses' => BookingStatus::cases(),
            'tourGuides' => User::whereIn('role', [UserRole::TOUR_GUIDE->value, UserRole::ADMIN->value])
                ->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status'   => ['required', Rule::enum(BookingStatus::class)],
            'agent_id' => [
                'nullable',
                Rule::exists('users', 'id')->whereIn('role', [UserRole::TOUR_GUIDE->value, UserRole::ADMIN->value]),
            ],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $booking->status;
        $new = BookingStatus::from($data['status']);

        // Booking yang sudah selesai atau dibatalkan bersifat final.
        if (in_array($old, [BookingStatus::COMPLETED, BookingStatus::CANCELLED], true) && $new !== $old) {
            return back()->with('error', "Booking berstatus {$old->value} tidak dapat diubah lagi.");
        }

        DB::transaction(function () use ($booking, $data, $old, $new) {
            $booking->update([
                'status'   => $new->value,
                'agent_id' => $data['agent_id'] ?? null,
                'notes'    => $data['notes'] ?? $booking->notes,
            ]);

            // Pembatalan mengembalikan kuota paket.
            if ($new === BookingStatus::CANCELLED && $old !== BookingStatus::CANCELLED) {
                TourPackage::whereKey($booking->tour_package_id)
                    ->increment('quota', $booking->total_participants);
            }
        });

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking berhasil diperbarui.');
    }
}