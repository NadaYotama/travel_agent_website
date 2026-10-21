<?php

namespace App\Http\Controllers\Manager;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Concerns\ResolvesVendor;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Report data is scoped to the manager's own vendor services. */
class DashboardController extends Controller
{
    use ResolvesVendor;

    public function index(Request $request): View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $vendor = $this->vendorOf($request);
        $start = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $end = Carbon::parse($request->input('end_date', now()))->endOfDay();
        $usesVendorServices = fn ($query, string $relation) => $query->whereHas(
            $relation,
            fn ($packages) => $packages->whereHas(
                'vendorServices',
                fn ($services) => $services->where('vendor_services.vendor_id', $vendor->id)
            )
        );
        $periodBookings = fn () => $usesVendorServices(
            Booking::query()->whereBetween('created_at', [$start, $end]),
            'tourPackage'
        );
        $verifiedPayments = fn () => $usesVendorServices(
            Payment::query()
                ->where('status', PaymentStatus::VERIFIED->value)
                ->whereBetween('paid_at', [$start, $end]),
            'booking.tourPackage'
        );

        $summary = [
            'revenue'       => (float) $verifiedPayments()->sum('amount'),
            'transactions'  => $verifiedPayments()->count(),
            'bookings'      => $periodBookings()->count(),
            'participants'  => (int) $periodBookings()
                ->where('status', '!=', BookingStatus::CANCELLED->value)
                ->sum('total_participants'),
            'booking_sales' => (float) $periodBookings()
                ->where('status', '!=', BookingStatus::CANCELLED->value)
                ->sum('total_price'),
        ];

        $services = $vendor->services()
            ->withCount('tourPackages')
            ->orderBy('name')
            ->get();

        $dailyRevenue = $verifiedPayments()
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $bookingsByStatus = $periodBookings()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $topPackages = $periodBookings()
            ->where('status', '!=', BookingStatus::CANCELLED->value)
            ->select(
                'tour_package_id',
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(total_price) as total_sales')
            )
            ->groupBy('tour_package_id')
            ->with('tourPackage:id,title')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        $latestBookings = $periodBookings()
            ->with(['user:id,name', 'tourPackage:id,title', 'latestPayment'])
            ->latest()
            ->limit(10)
            ->get();

        return view('manager.dashboard', compact(
            'vendor',
            'start',
            'end',
            'summary',
            'dailyRevenue',
            'bookingsByStatus',
            'topPackages',
            'services',
            'latestBookings'
        ));
    }
}
