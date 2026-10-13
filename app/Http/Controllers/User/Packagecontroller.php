<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\TourPackage;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** Katalog paket (publik). Parameter pencarian cocok dengan form di welcome.blade.php. */
class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q'              => ['nullable', 'string', 'max:100'],
            'bulan'          => ['nullable', 'date_format:Y-m'],
            'peserta'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'destination_id' => ['nullable', 'integer'],
            'sort'           => ['nullable', 'in:terdekat,termurah,termahal'],
        ]);

        $packages = TourPackage::query()
            ->where('is_published', true)
            ->whereDate('start_date', '>=', today())
            ->with(['destination:id,name,country', 'images'])
            ->withAvg('reviews', 'rating')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(fn ($q) => $q
                    ->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('destination', fn ($d) => $d
                        ->where('name', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhere('country', 'like', "%{$keyword}%")));
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $month = Carbon::createFromFormat('Y-m', $request->input('bulan'));
                $query->whereBetween('start_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()]);
            })
            ->when($request->filled('peserta'), fn ($q) => $q->where('quota', '>=', $request->integer('peserta')))
            ->when($request->filled('destination_id'), fn ($q) => $q->where('destination_id', $request->integer('destination_id')))
            ->when(
                $request->input('sort'),
                fn ($q, $sort) => match ($sort) {
                    'termurah' => $q->orderBy('price'),
                    'termahal' => $q->orderByDesc('price'),
                    default    => $q->orderBy('start_date'),
                },
                fn ($q) => $q->orderBy('start_date')
            )
            ->paginate(9)
            ->withQueryString();

        return view('user.packages.index', [
            'packages'     => $packages,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(TourPackage $package): View
    {
        $this->authorize('view', $package);

        $package->load([
            'destination',
            'itineraries' => fn ($q) => $q->orderBy('day'),
            'images',
            'reviews'     => fn ($q) => $q->with('user:id,name,avatar')->latest()->limit(10),
        ]);
        $package->loadAvg('reviews', 'rating')->loadCount('reviews');

        $related = TourPackage::query()
            ->where('is_published', true)
            ->where('destination_id', $package->destination_id)
            ->whereKeyNot($package->id)
            ->whereDate('start_date', '>=', today())
            ->with('images')
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        return view('user.packages.show', compact('package', 'related'));
    }
}