@extends('layouts.agent')

@section('title', 'Dashboard Agen')
@section('header', 'Panel Kerja Agen')

@section('content')
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-warning text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-clock"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['pending_payments'] ?? 0 }}</h3>
                <h6 class="text-white">Menunggu Pembayaran</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-cyan text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-help-circle"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['unassigned'] ?? 0 }}</h3>
                <h6 class="text-white">Belum Ditugaskan</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-info text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-account-check"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['my_active'] ?? 0 }}</h3>
                <h6 class="text-white">Pesanan Aktif Saya</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-success text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-check-circle"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['my_completed'] ?? 0 }}</h3>
                <h6 class="text-white">Selesai Ditangani</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pesanan Aktif yang Saya Tangani</h4>
                <div class="table-responsive m-t-20">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Pelanggan</th>
                                <th>Paket Wisata</th>
                                <th>Tanggal Berangkat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myBookings ?? [] as $b)
                                <tr>
                                    <td class="font-weight-bold">{{ $b->user->name ?? '-' }}</td>
                                    <td>{{ $b->tourPackage->title ?? '-' }}</td>
                                    <td>{{ optional($b->tourPackage->start_date)->format('d M Y') ?? '-' }}</td>
                                    <td><span class="badge badge-success p-2">{{ ucfirst($b->status) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada pesanan aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
