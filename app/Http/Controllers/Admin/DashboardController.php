<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'customers'        => User::where('role', UserRole::USER->value)->count(),
            'packages'         => TourPackage::where('is_published', true)->count(),
            'destinations'     => Destination::count(),
            'bookings'         => Booking::count(),
            'pending_payments' => Payment::where('status', PaymentStatus::PENDING->value)->count(),
            'revenue'          => (float) Payment::where('status', PaymentStatus::VERIFIED->value)->sum('amount'),
        ];

        // Pendapatan 6 bulan terakhir (untuk grafik)
        $monthlyRevenue = collect(range(5, 0))->map(function (int $ago) {
            $start = now()->subMonths($ago)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            return [
                'label' => $start->translatedFormat('M Y'),
                'total' => (float) Payment::where('status', PaymentStatus::VERIFIED->value)
                    ->whereBetween('paid_at', [$start, $end])
                    ->sum('amount'),
            ];
        });

        $bookingsByStatus = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentBookings = Booking::with(['user:id,name', 'tourPackage:id,title'])
            ->latest()
            ->limit(8)
            ->get();

        // Top 5 destinasi berdasarkan jumlah paket yang dipublikasikan
        $topDestinations = Destination::withCount([
                'tourPackages as published_count' => fn ($q) => $q->where('is_published', true),
            ])
            ->orderByDesc('published_count')
            ->limit(5)
            ->get();

        // 6 paket wisata terbaru yang sudah dipublikasikan
        $latestPackages = TourPackage::where('is_published', true)
            ->with(['images', 'destination:id,name'])
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyRevenue',
            'bookingsByStatus',
            'recentBookings',
            'topDestinations',
            'latestPackages'
        ));
    }
}