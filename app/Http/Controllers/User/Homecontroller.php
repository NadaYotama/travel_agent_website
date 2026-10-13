<?php

namespace App\Http\Controllers\User;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\TourPackage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /** Halaman publik (welcome.blade.php). Bila data kosong, view memakai data contoh. */
    public function welcome(): View
    {
        $destinations = Destination::query()
            ->withCount(['tourPackages as published_count' => fn ($q) => $q->where('is_published', true)])
            ->orderByDesc('published_count')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Destination $d) => [
                'id'      => $d->id,
                'name'    => $d->name,
                'region'  => $d->city ? "{$d->city}, {$d->country}" : $d->country,
                'img_url' => $d->thumbnail ? Storage::url($d->thumbnail) : null,
                'count'   => $d->published_count,
            ]);

        $packages = TourPackage::query()
            ->where('is_published', true)
            ->with('images')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (TourPackage $p) => [
                'id'      => $p->id,
                'title'   => $p->title,
                'days'    => $p->duration_days,
                'price'   => $p->price,
                'quota'   => $p->quota,
                'img_url' => $p->images->first() ? Storage::url($p->images->first()->path) : null,
                'tag'     => $p->quota <= 5 && $p->quota > 0 ? "Sisa {$p->quota} kursi" : ($p->quota == 0 ? 'Penuh' : 'Pilihan'),
                'url'     => route('user.packages.show', $p->slug),
            ]);

        return view('welcome', [
            'destinations' => $destinations->isNotEmpty() ? $destinations->all() : null,
            'packages'     => $packages->isNotEmpty() ? $packages->all() : null,
        ]);
    }

    /** Dashboard customer. */
    public function index(Request $request): View
    {
        $user = $request->user();

        $upcoming = $user->bookings()
            ->with('tourPackage:id,title,slug,start_date,end_date')
            ->where('status', BookingStatus::CONFIRMED->value)
            ->whereHas('tourPackage', fn ($q) => $q->whereDate('end_date', '>=', today()))
            ->get()
            ->sortBy('tourPackage.start_date')
            ->values();

        $stats = [
            'total'            => $user->bookings()->count(),
            'awaiting_payment' => $user->bookings()->where('status', BookingStatus::PENDING->value)->count(),
            'in_verification'  => $user->bookings()->where('status', BookingStatus::PAID->value)->count(),
            'completed'        => $user->bookings()->where('status', BookingStatus::COMPLETED->value)->count(),
        ];

        $latestBookings = $user->bookings()
            ->with('tourPackage:id,title,slug')
            ->latest()
            ->limit(5)
            ->get();

        return view('user.home', compact('stats', 'upcoming', 'latestBookings'));
    }
}