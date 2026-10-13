<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Laporan penjualan. Filter: ?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
     * Tambahkan &export=csv untuk mengunduh data booking pada periode tersebut.
     */
    public function index(Request $request): View|StreamedResponse
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'export'     => ['nullable', 'in:csv'],
        ]);

        $start = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $end   = Carbon::parse($request->input('end_date', now()))->endOfDay();

        if ($request->input('export') === 'csv') {
            return $this->exportCsv($start, $end);
        }

        $verified = fn () => Payment::where('status', PaymentStatus::VERIFIED->value)
            ->whereBetween('paid_at', [$start, $end]);

        $summary = [
            'revenue'      => (float) $verified()->sum('amount'),
            'transactions' => $verified()->count(),
            'bookings'     => Booking::whereBetween('created_at', [$start, $end])->count(),
            'participants' => (int) Booking::whereBetween('created_at', [$start, $end])
                ->where('status', '!=', BookingStatus::CANCELLED->value)
                ->sum('total_participants'),
        ];

        $dailyRevenue = $verified()
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $bookingsByStatus = Booking::whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $topPackages = Booking::whereBetween('created_at', [$start, $end])
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

        return view('admin.reports.index', [
            'start'            => $start,
            'end'              => $end,
            'summary'          => $summary,
            'dailyRevenue'     => $dailyRevenue,
            'bookingsByStatus' => $bookingsByStatus,
            'topPackages'      => $topPackages,
        ]);
    }

    private function exportCsv(Carbon $start, Carbon $end): StreamedResponse
    {
        $filename = sprintf('laporan-booking_%s_%s.csv', $start->format('Ymd'), $end->format('Ymd'));

        return response()->streamDownload(function () use ($start, $end) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar terbaca benar di Excel

            fputcsv($out, ['Kode', 'Tanggal', 'Customer', 'Paket', 'Peserta', 'Total', 'Status']);

            Booking::with(['user:id,name', 'tourPackage:id,title'])
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('created_at')
                ->lazy(500)
                ->each(function (Booking $b) use ($out) {
                    fputcsv($out, [
                        $b->code,
                        $b->created_at->format('Y-m-d H:i'),
                        $b->user?->name,
                        $b->tourPackage?->title,
                        $b->total_participants,
                        $b->total_price,
                        $b->status->value,
                    ]);
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}