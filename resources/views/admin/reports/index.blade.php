@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Transaksi')
@section('header', 'Laporan Penjualan')

@section('content')
<!-- Filter Periode Laporan -->
<div class="row m-b-20">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <form action="{{ route('admin.reports.index') }}" method="GET" class="row align-items-center">
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="font-weight-bold text-dark font-12 mb-1">Dari Tanggal:</label>
                        <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="font-weight-bold text-dark font-12 mb-1">Sampai Tanggal:</label>
                        <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-3 col-sm-6 text-md-left mt-2 mt-md-0 pt-md-3">
                        <button type="submit" class="btn btn-cyan waves-effect">
                            <i class="mdi mdi-filter m-r-5"></i> Terapkan Filter
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 text-md-right mt-2 mt-md-0 pt-md-3">
                        <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success waves-effect">
                            <i class="mdi mdi-file-excel m-r-5"></i> Unduh CSV Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Statistik Kartu -->
<div class="row">
    <div class="col-md-3">
        <div class="card card-hover">
            <div class="box bg-success text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-currency-usd"></i></h1>
                <h4 class="text-white m-b-0 font-weight-bold">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</h4>
                <h6 class="text-white">Total Pendapatan Terverifikasi</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-hover">
            <div class="box bg-cyan text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-receipt"></i></h1>
                <h4 class="text-white m-b-0 font-weight-bold">{{ $summary['bookings'] }}</h4>
                <h6 class="text-white">Total Pemesanan Dibuat</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-hover">
            <div class="box bg-warning text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-account-multiple-check"></i></h1>
                <h4 class="text-white m-b-0 font-weight-bold">{{ $summary['participants'] }}</h4>
                <h6 class="text-white">Total Peserta Wisata</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-hover">
            <div class="box bg-info text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-credit-card-check"></i></h1>
                <h4 class="text-white m-b-0 font-weight-bold">{{ $summary['transactions'] }}</h4>
                <h6 class="text-white">Transaksi Pembayaran Lunas</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Paket Wisata Terlaris -->
    <div class="col-lg-7 col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-15">Top 10 Paket Wisata Paling Laris</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Paket Wisata</th>
                                <th class="text-center">Jumlah Booking</th>
                                <th>Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPackages as $idx => $tp)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">{{ $idx + 1 }}</td>
                                    <td class="align-middle font-weight-bold text-dark">{{ $tp->tourPackage->title ?? '-' }}</td>
                                    <td class="text-center align-middle font-weight-bold">
                                        <span class="badge badge-info p-2">{{ $tp->total_bookings }} Pesanan</span>
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($tp->total_sales, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data penjualan pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Pemesanan -->
    <div class="col-lg-5 col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-15">Komposisi Status Booking</h4>
                <ul class="list-group list-group-flush font-14">
                    @forelse($bookingsByStatus as $st => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-capitalize font-weight-bold">{{ $st }}</span>
                            <span class="badge badge-primary badge-pill font-13">{{ $count }} Booking</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Belum ada booking pada rentang tanggal ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

