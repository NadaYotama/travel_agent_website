<?php

namespace App\Http\Controllers\User;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\TourPackage;
use App\Support\DemoGallery;
use App\Support\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /** Jumlah kartu destinasi di halaman depan. */
    private const DESTINATION_CARDS = 5;

    /** Jumlah kartu paket di halaman depan. */
    private const PACKAGE_CARDS = 6;

    /** Sisa kuota yang sudah ditandai "Sisa n kursi". */
    private const LOW_QUOTA = 5;

    /**
     * Halaman publik (welcome.blade.php).
     *
     * Foto contoh diambil dari disk public agar bisa diakses melalui storage:link.
     */
    public function welcome(): View
    {
        $destinations = Destination::query()
            ->withCount(['tourPackages as published_count' => fn ($q) => $q->where('is_published', true)])
            ->orderByDesc('published_count')
            ->latest()
            ->limit(self::DESTINATION_CARDS)
            ->get()
            ->map(function (Destination $destination) {
                $imagePath = $destination->thumbnail;

                if (! Media::exists($imagePath)) {
                    $imagePath = DemoGallery::destinationPath($destination->slug);
                }

                return [
                    'id'      => $destination->id,
                    'name'    => $destination->name,
                    'region'  => $destination->city
                        ? "{$destination->city}, {$destination->country}"
                        : $destination->country,
                    'img_url' => Media::url($imagePath),
                    'count'   => $destination->published_count,
                ];
            })
            ->all();

        $packages = TourPackage::query()
            ->where('is_published', true)
            ->with('images')
            ->latest()
            ->limit(self::PACKAGE_CARDS)
            ->get()
            ->map(function (TourPackage $package) {
                $imagePath = $package->images->first()?->path;

                if (! Media::exists($imagePath)) {
                    $imagePath = DemoGallery::packagePath($package->slug);
                }

                return [
                    'id'      => $package->id,
                    'title'   => $package->title,
                    'days'    => $package->duration_days,
                    'price'   => $package->price,
                    'quota'   => $package->quota,
                    'img_url' => Media::url($imagePath),
                    'tag'     => $this->quotaTag($package->quota),
                    'url'     => route('user.packages.show', $package->slug),
                ];
            })
            ->all();

        return view('welcome', [
            'destinations' => $destinations,
            'packages'     => $packages,
            'heroUrl'      => $this->heroUrl(),
            'dashboard'    => $this->dashboardUrl(),
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

    /**
     * Foto latar hero. Memakai salinan berukuran web bila sudah
     * dipublikasikan, dan kembali ke foto asli bila belum.
     */
    private function heroUrl(): string
    {
        return (string) Media::url(DemoGallery::HERO_TARGET);
    }

    /** Label sisa kuota pada kartu paket. */
    private function quotaTag(int $quota): string
    {
        if ($quota === 0) {
            return 'Penuh';
        }

        return $quota <= self::LOW_QUOTA ? "Sisa {$quota} kursi" : 'Pilihan';
    }

    /** Tautan dashboard sesuai peran pengguna yang sedang masuk. */
    private function dashboardUrl(): ?string
    {
        return auth()->check() ? route(auth()->user()->role->dashboardRoute()) : null;
    }
}
