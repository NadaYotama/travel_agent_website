<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Ulasan hanya untuk booking berstatus completed; satu ulasan per customer per paket. */
class ReviewController extends Controller
{
    public function create(Request $request, Booking $booking): View
    {
        $this->authorize('review', $booking);

        $booking->load('tourPackage:id,title,slug');

        $review = Review::where('user_id', $request->user()->id)
            ->where('tour_package_id', $booking->tour_package_id)
            ->first();

        return view('user.reviews.create', compact('booking', 'review'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('review', $booking);

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Mengirim ulang akan memperbarui ulasan sebelumnya, bukan membuat duplikat.
        Review::updateOrCreate(
            [
                'user_id'         => $request->user()->id,
                'tour_package_id' => $booking->tour_package_id,
            ],
            $data
        );

        return redirect()->route('user.bookings.show', $booking->code)
            ->with('success', 'Terima kasih, ulasan Anda tersimpan.');
    }
}