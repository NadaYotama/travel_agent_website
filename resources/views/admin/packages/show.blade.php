@extends('layouts.admin')

@section('title', 'Detail Paket: ' . $package->title)
@section('header', 'Detail Paket Wisata')

@section('content')
<div class="row">
    <!-- Kolom Kiri: Informasi Utama, Galeri, Itinerary, Review -->
    <div class="col-lg-8 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                    <div>
                        <span class="badge badge-info p-2 font-12 mb-2">{{ $package->destination->name ?? 'Destinasi' }}</span>
                        <h3 class="font-weight-bold text-dark mb-0">{{ $package->title }}</h3>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-cyan btn-sm waves-effect m-r-5">
                            <i class="mdi mdi-pencil m-r-5"></i> Edit Paket
                        </a>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-sm waves-effect">
                            <i class="mdi mdi-arrow-left m-r-5"></i> Kembali
                        </a>
                    </div>
                </div>

                <!-- Galeri Foto -->
                <h5 class="font-weight-bold text-dark m-b-15"><i class="mdi mdi-image-multiple m-r-5"></i> Galeri Foto</h5>
                @if($package->images->count() > 0)
                    <div class="row m-b-25">
                        @foreach($package->images as $img)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ Storage::url($img->path) }}" target="_blank">
                                    <img src="{{ Storage::url($img->path) }}" class="img-fluid rounded border shadow-sm" style="height: 140px; width: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/Balii.jpg') }}'">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted font-13 m-b-25">Belum ada foto yang diunggah.</p>
                @endif

                <!-- Deskripsi Paket -->
                <h5 class="font-weight-bold text-dark m-b-15 border-top p-t-15"><i class="mdi mdi-text-subject m-r-5"></i> Deskripsi Paket</h5>
                <div class="text-dark font-14 m-b-25 leading-relaxed">
                    {!! nl2br(e($package->description)) !!}
                </div>

                <!-- Itinerary Rencana Perjalanan -->
                <h5 class="font-weight-bold text-dark m-b-15 border-top p-t-15"><i class="mdi mdi-calendar-text m-r-5"></i> Rencana Perjalanan (Itinerary)</h5>
                @if($package->itineraries->count() > 0)
                    <div class="space-y-3">
                        @foreach($package->itineraries as $it)
                            <div class="p-3 bg-light rounded border m-b-10">
                                <div class="font-weight-bold text-cyan font-14">Hari {{ $it->day }}: {{ $it->title }}</div>
                                @if($it->description)
                                    <div class="font-13 text-muted mt-1">{{ $it->description }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted font-13">Belum ada jadwal itinerary yang diatur.</p>
                @endif

                <!-- Ulasan Pelanggan -->
                <h5 class="font-weight-bold text-dark m-b-15 border-top p-t-20"><i class="mdi mdi-star m-r-5"></i> Ulasan Customer</h5>
                @forelse($package->reviews as $rev)
                    <div class="border-bottom p-b-10 m-b-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-dark">{{ $rev->user->name ?? 'Customer' }}</strong>
                            <span class="text-warning font-14">{{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}</span>
                        </div>
                        <p class="font-13 text-muted mb-0 mt-1">{{ $rev->comment }}</p>
                    </div>
                @empty
                    <p class="text-muted font-13">Belum ada ulasan untuk paket ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Ringkasan Status & Statistik Paket -->
    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-20 border-bottom p-b-10">Status & Ringkasan</h4>

                <div class="text-center m-b-20">
                    <span class="font-12 text-muted">Status Publikasi</span>
                    <div class="mt-1">
                        @if($package->is_published)
                            <span class="badge badge-success font-14 p-2 px-3">Dipublikasikan</span>
                        @else
                            <span class="badge badge-warning text-dark font-14 p-2 px-3">Draf / Disembunyikan</span>
                        @endif
                    </div>
                </div>

                <ul class="list-group list-group-flush font-14">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Harga / Orang:</span>
                        <strong class="text-success font-16">Rp {{ number_format($package->price, 0, ',', '.') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Durasi Wisata:</span>
                        <strong class="text-dark">{{ $package->duration_days }} Hari</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Total Kuota:</span>
                        <strong class="text-dark">{{ $package->quota }} Peserta</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tanggal Mulai:</span>
                        <strong class="text-dark">{{ optional($package->start_date)->format('d M Y') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tanggal Selesai:</span>
                        <strong class="text-dark">{{ optional($package->end_date)->format('d M Y') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 bg-light p-3 rounded mt-2">
                        <span class="font-weight-bold text-dark">Total Booking:</span>
                        <span class="badge badge-info font-14">{{ $package->bookings_count ?? 0 }} Pesanan</span>
                    </li>
                </ul>

                <div class="m-t-25">
                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-cyan btn-block font-weight-bold waves-effect">
                        <i class="mdi mdi-pencil m-r-5"></i> Edit Paket Wisata
                    </a>
                    <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="m-t-10" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket wisata ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-block waves-effect">
                            <i class="mdi mdi-delete m-r-5"></i> Hapus Paket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

