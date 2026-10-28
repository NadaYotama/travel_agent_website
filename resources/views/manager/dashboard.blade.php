@extends('layouts.manager')

@section('title', 'Laporan Manager')
@section('header', 'Laporan Manager')

@section('content')
<!-- <div class="card bg-dark text-white m-b-20">
    <div class="card-body d-flex flex-wrap align-items-center"> -->
        <!-- <div>
            <h3 class="card-title text-white font-weight-bold mb-1">{{ $vendor->company_name ?? 'Manager' }}</h3>
            <p class="card-text text-muted mb-0">{{ $vendor->address ?? 'Laporan hanya mencakup layanan manager ini.' }}</p>
        </div> -->
        <!-- <a href="{{ route('manager.services.index') }}" class="ml-auto btn btn-outline-light m-t-10">
            <i class="mdi mdi-hotel m-r-5"></i> Kelola Layanan
        </a> -->
    <!-- </div>
</div> -->

<div class="card bg-light m-b-20">
    <div class="card-body">
        <form action="{{ route('manager.dashboard') }}" method="GET" class="row align-items-end">
            <div class="col-md-3 col-sm-6 mb-2">
                <label for="start_date" class="font-weight-bold text-dark">Dari tanggal</label>
                <input id="start_date" type="date" name="start_date" value="{{ $start->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <label for="end_date" class="font-weight-bold text-dark">Sampai tanggal</label>
                <input id="end_date" type="date" name="end_date" value="{{ $end->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-cyan waves-effect">
                    <i class="mdi mdi-filter m-r-5"></i> Terapkan Filter
                </button>
            </div>
            <div class="col-md-3 mb-2 text-md-right">
                <small class="text-muted">Periode laporan: {{ $start->format('d M Y') }} – {{ $end->format('d M Y') }}</small>
            </div>
        </form>
        @if ($errors->any())
            <div class="alert alert-danger mb-0 mt-3">{{ $errors->first() }}</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover"><div class="box bg-success text-center">
            <h1 class="font-light text-white"><i class="mdi mdi-currency-usd"></i></h1>
            <h4 class="text-white m-b-0 font-weight-bold">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</h4>
            <h6 class="text-white">Pembayaran Terverifikasi</h6>
        </div></div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover"><div class="box bg-info text-center">
            <h1 class="font-light text-white"><i class="mdi mdi-cash-multiple"></i></h1>
            <h4 class="text-white m-b-0 font-weight-bold">Rp {{ number_format($summary['booking_sales'], 0, ',', '.') }}</h4>
            <h6 class="text-white">Nilai Bruto Booking</h6>
        </div></div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover"><div class="box bg-cyan text-center">
            <h1 class="font-light text-white"><i class="mdi mdi-receipt"></i></h1>
            <h4 class="text-white m-b-0 font-weight-bold">{{ $summary['bookings'] }}</h4>
            <h6 class="text-white">Booking Dibuat</h6>
        </div></div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover"><div class="box bg-warning text-center">
            <h1 class="font-light text-white"><i class="mdi mdi-account-multiple-check"></i></h1>
            <h4 class="text-white m-b-0 font-weight-bold">{{ $summary['participants'] }}</h4>
            <h6 class="text-white">Peserta (Tidak Termasuk Batal)</h6>
        </div></div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover"><div class="box bg-primary text-center">
            <h1 class="font-light text-white"><i class="mdi mdi-credit-card-check"></i></h1>
            <h4 class="text-white m-b-0 font-weight-bold">{{ $summary['transactions'] }}</h4>
            <h6 class="text-white">Pembayaran Terverifikasi</h6>
        </div></div>
    </div>
</div>

<div class="alert alert-info">
    <strong>Catatan:</strong> nilai pembayaran dan booking adalah nilai bruto seluruh booking paket yang menggunakan minimal satu layanan Anda. Ini bukan perhitungan laba atau bagian bersih manager.
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Paket Terkait Berdasarkan Nilai Booking</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light"><tr><th>#</th><th>Paket</th><th>Jumlah Booking</th><th>Nilai Bruto</th></tr></thead>
                        <tbody>
                            @forelse($topPackages as $index => $package)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $package->tourPackage->title ?? '-' }}</td>
                                    <td>{{ $package->total_bookings }}</td>
                                    <td>Rp {{ number_format($package->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada booking pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Status Booking</h4>
                <ul class="list-group list-group-flush">
                    @forelse($bookingsByStatus as $status => $count)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-capitalize">{{ $status }}</span>
                            <span class="badge badge-primary badge-pill">{{ $count }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Tidak ada booking pada periode ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tren Pembayaran Terverifikasi</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="thead-light"><tr><th>Tanggal Pembayaran</th><th class="text-right">Total</th></tr></thead>
                        <tbody>
                            @forelse($dailyRevenue as $day)
                                <tr><td>{{ \Illuminate\Support\Carbon::parse($day->date)->format('d M Y') }}</td><td class="text-right">Rp {{ number_format($day->total, 0, ',', '.') }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-4">Belum ada pembayaran terverifikasi pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Layanan Manager</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="thead-light"><tr><th>Layanan</th><th>Jenis</th><th>Paket</th></tr></thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr><td>{{ $service->name }}</td><td>{{ $service->type_label }}</td><td>{{ $service->tour_packages_count }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada layanan. <a href="{{ route('manager.services.create') }}">Tambah layanan</a></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Booking Terbaru dalam Periode</h4>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-light"><tr><th>Kode</th><th>Customer</th><th>Paket</th><th>Tanggal</th><th>Status Booking</th><th>Status Pembayaran</th><th class="text-right">Nilai</th></tr></thead>
                <tbody>
                    @forelse($latestBookings as $booking)
                        <tr>
                            <td>{{ $booking->code }}</td>
                            <td>{{ $booking->user->name ?? '-' }}</td>
                            <td>{{ $booking->tourPackage->title ?? '-' }}</td>
                            <td>{{ $booking->created_at->format('d M Y') }}</td>
                            <td>{{ $booking->status->value }}</td>
                            <td>{{ $booking->latestPayment?->status->value ?? 'Belum ada' }}</td>
                            <td class="text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada booking pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
