@extends('layouts.vendor')

@section('title', 'Dashboard Vendor')
@section('header', 'Panel Vendor')

@section('content')
<div class="card bg-dark text-white m-b-20">
    <div class="card-body">
        <h3 class="card-title text-white font-weight-bold">{{ $vendor->company_name ?? 'Mitra Vendor' }}</h3>
        <p class="card-text text-muted mb-0"><i class="mdi mdi-map-marker m-r-5"></i>{{ $vendor->address ?? 'Alamat operasional mitra vendor' }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card card-hover">
            <div class="box bg-cyan text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-hotel"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['services'] ?? 0 }}</h3>
                <h6 class="text-white">Layanan Terdaftar</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover">
            <div class="box bg-warning text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-airplane"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['packages'] ?? 0 }}</h3>
                <h6 class="text-white">Paket Wisata Terkait</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover">
            <div class="box bg-success text-center">
                <h1 class="font-light text-white"><i class="mdi mdi-account-multiple-check"></i></h1>
                <h3 class="text-white m-b-0">{{ $stats['participants'] ?? 0 }}</h3>
                <h6 class="text-white">Peserta Terkonfirmasi</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Paket Wisata Mendatang yang Menggunakan Layanan Anda</h4>
                <div class="m-t-20">
                    @forelse($upcomingPackages ?? [] as $pkg)
                        <div class="d-flex no-block align-items-center p-15 border-bottom">
                            <div>
                                <h5 class="m-b-5 font-weight-bold text-dark">{{ $pkg->title }}</h5>
                                <span class="text-muted font-12">
                                    <i class="mdi mdi-calendar m-r-5"></i>Mulai: {{ optional($pkg->start_date)->format('d M Y') }} &bull; Selesai: {{ optional($pkg->end_date)->format('d M Y') }}
                                </span>
                            </div>
                            <div class="ml-auto">
                                <span class="badge badge-info p-2 font-12">{{ $pkg->confirmed_participants ?? 0 }} Peserta</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-4">Belum ada paket wisata mendatang.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
