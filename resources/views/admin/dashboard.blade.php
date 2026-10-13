@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard Utama')

@section('content')
<!-- Sales Cards / Widget Angka Matrix Admin -->
<div class="row">
    <!-- Total Pelanggan -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-cyan text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-account-multiple"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['customers'] ?? 0 }}</h3>
                <h6 class="text-white">Total Pelanggan</h6>
            </div>
        </div>
    </div>
    <!-- Paket Aktif -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-success text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-airplane-takeoff"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['packages'] ?? 0 }}</h3>
                <h6 class="text-white">Paket Wisata Aktif</h6>
            </div>
        </div>
    </div>
    <!-- Total Pesanan -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-warning text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-receipt"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['bookings'] ?? 0 }}</h3>
                <h6 class="text-white">Total Pesanan</h6>
            </div>
        </div>
    </div>
    <!-- Total Pendapatan -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-danger text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-currency-usd"></i></h1>
                <h4 class="text-white m-b-0">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</h4>
                <h6 class="text-white">Total Pendapatan</h6>
            </div>
        </div>
    </div>
</div>

<!-- Row Tabel Pesanan & Ringkasan Keuangan -->
<div class="row">
    <!-- Tabel Pesanan Terbaru -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Pesanan Masuk Terbaru</h4>
                        <h5 class="card-subtitle">Daftar transaksi dan booking paket perjalanan terkini</h5>
                    </div>
                </div>
                <div class="table-responsive m-t-20">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Pelanggan</th>
                                <th scope="col">Paket Wisata</th>
                                <th scope="col">Tanggal Pesan</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $b)
                                <tr>
                                    <td class="font-weight-bold">{{ $b->user->name ?? '-' }}</td>
                                    <td>{{ $b->tourPackage->title ?? '-' }}</td>
                                    <td>{{ $b->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($b->status) {
                                                'confirmed' => 'badge-success',
                                                'paid'      => 'badge-info',
                                                'pending'   => 'badge-warning',
                                                'cancelled' => 'badge-danger',
                                                default     => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} p-2">{{ ucfirst($b->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data pesanan terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Informasi & Shortcut Cepat -->
    <div class="col-lg-4">
        <!-- Status Pembayaran Menunggu -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Menunggu Verifikasi</h4>
                <div class="d-flex align-items-center m-t-15">
                    <span class="btn btn-warning btn-circle font-20 text-white"><i class="mdi mdi-clock"></i></span>
                    <div class="m-l-15">
                        <h3 class="m-b-0 font-weight-bold text-warning">{{ $stats['pending_payments'] ?? 0 }}</h3>
                        <span class="text-muted">Pembayaran pending perlu ditinjau</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Pintasan Cepat -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pintasan Cepat</h4>
                <div class="list-group m-t-15">
                    <a href="{{ route('admin.packages.create') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="mdi mdi-plus-circle text-success m-r-10"></i> Tambah Paket Wisata</span>
                        <i class="ti-angle-right"></i>
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="mdi mdi-format-list-bulleted text-info m-r-10"></i> Kelola Pemesanan</span>
                        <i class="ti-angle-right"></i>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="mdi mdi-file-chart text-danger m-r-10"></i> Cetak Laporan</span>
                        <i class="ti-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
