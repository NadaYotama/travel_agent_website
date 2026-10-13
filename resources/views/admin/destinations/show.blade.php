@extends('layouts.admin')

@section('title', 'Detail Destinasi: ' . $destination->name)
@section('header', 'Detail Destinasi')

@section('content')
<div class="row">
    <!-- Kolom Kiri: Info Destinasi & Daftar Paket Terkait -->
    <div class="col-lg-8 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20 border-bottom p-b-15">
                    <div>
                        <span class="badge badge-info p-2 font-12 mb-2">{{ $destination->country }}</span>
                        <h3 class="font-weight-bold text-dark mb-0">{{ $destination->name }}</h3>
                        <span class="text-muted font-13"><i class="mdi mdi-map-marker text-danger"></i> {{ $destination->city }}</span>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.destinations.edit', $destination) }}" class="btn btn-cyan btn-sm waves-effect m-r-5">
                            <i class="mdi mdi-pencil m-r-5"></i> Edit Destinasi
                        </a>
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary btn-sm waves-effect">
                            <i class="mdi mdi-arrow-left m-r-5"></i> Kembali
                        </a>
                    </div>
                </div>

                @if($destination->thumbnail)
                    <div class="m-b-25 rounded overflow-hidden border">
                        <img src="{{ Storage::url($destination->thumbnail) }}" alt="{{ $destination->name }}" 
                             class="w-100" style="max-height: 300px; object-fit: cover;" onerror="this.src='{{ asset('images/Balii.jpg') }}'">
                    </div>
                @endif

                <h5 class="font-weight-bold text-dark m-b-10">Tentang Destinasi</h5>
                <p class="text-dark font-14 leading-relaxed m-b-25">
                    {{ $destination->description ?: 'Belum ada deskripsi yang ditambahkan untuk destinasi ini.' }}
                </p>

                <!-- Daftar Paket Wisata Terkait -->
                <h5 class="font-weight-bold text-dark m-b-15 border-top p-t-20">
                    <i class="mdi mdi-airplane m-r-5"></i> Paket Wisata di Destinasi Ini ({{ $destination->tourPackages->count() }})
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Paket</th>
                                <th>Durasi</th>
                                <th>Jadwal</th>
                                <th>Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($destination->tourPackages as $pkg)
                                <tr>
                                    <td class="align-middle font-weight-bold text-dark">{{ $pkg->title }}</td>
                                    <td class="align-middle">{{ $pkg->duration_days }} Hari</td>
                                    <td class="align-middle font-12">
                                        {{ optional($pkg->start_date)->format('d M Y') }} - {{ optional($pkg->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($pkg->is_published)
                                            <span class="badge badge-success p-1">Aktif</span>
                                        @else
                                            <span class="badge badge-warning text-dark p-1">Draf</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.packages.show', $pkg) }}" class="btn btn-cyan btn-sm">
                                            <i class="mdi mdi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada paket wisata yang menggunakan destinasi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Ringkasan & Aksi -->
    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-20 border-bottom p-b-10">Ringkasan Destinasi</h4>

                <ul class="list-group list-group-flush font-14">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Slug URL:</span>
                        <code class="text-cyan">{{ $destination->slug }}</code>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Kota / Daerah:</span>
                        <strong class="text-dark">{{ $destination->city }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Negara:</span>
                        <strong class="text-dark">{{ $destination->country }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 bg-light p-3 rounded mt-2">
                        <span class="font-weight-bold text-dark">Total Paket:</span>
                        <span class="badge badge-info font-14">{{ $destination->tourPackages->count() }} Paket Wisata</span>
                    </li>
                </ul>

                <div class="m-t-25">
                    <a href="{{ route('admin.destinations.edit', $destination) }}" class="btn btn-cyan btn-block font-weight-bold waves-effect">
                        <i class="mdi mdi-pencil m-r-5"></i> Edit Destinasi
                    </a>
                    <form action="{{ route('admin.destinations.destroy', $destination) }}" method="POST" class="m-t-10" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-block waves-effect" {{ $destination->tourPackages->count() > 0 ? 'disabled title="Masih digunakan paket"' : '' }}>
                            <i class="mdi mdi-delete m-r-5"></i> Hapus Destinasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

