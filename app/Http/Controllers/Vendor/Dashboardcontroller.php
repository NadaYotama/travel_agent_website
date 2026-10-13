<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\BookingStatus;
use App\Http\Controllers\Concerns\ResolvesVendor;
use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ResolvesVendor;

    public function index(Request $request): View
    {
        $vendor = $this->vendorOf($request);

        // Paket yang belum berakhir dan memakai layanan milik vendor ini,
        // lengkap dengan jumlah peserta dari booking yang sudah terkonfirmasi.
        $upcomingPackages = TourPackage::query()
            ->whereHas('vendorServices', fn ($q) => $q->where('vendor_services.vendor_id', $vendor->id))
            ->whereDate('end_date', '>=', today())
            ->with(['vendorServices' => fn ($q) => $q->where('vendor_services.vendor_id', $vendor->id)])
            ->withSum(
                ['bookings as confirmed_participants' => function ($q) {
                    $q->where('status', '=', BookingStatus::CONFIRMED->value);
                }],
                'total_participants'
            )
            ->orderBy('start_date')
            ->get();

        $stats = [
            'services'     => $vendor->services()->count(),
            'packages'     => $upcomingPackages->count(),
            'participants' => (int) $upcomingPackages->sum('confirmed_participants'),
        ];

        return view('vendor.dashboard', compact('vendor', 'stats', 'upcomingPackages'));
    }
}