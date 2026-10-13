@extends('layouts.admin')

@section('title', 'Kelola Destinasi')
@section('header', 'Destinasi Wisata')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Daftar Destinasi Wisata</h4>
                        <p class="text-muted font-13 mb-0">Kelola kota, wilayah, dan negara tujuan liburan yang tersedia untuk paket wisata.</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.destinations.create') }}" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-plus m-r-5"></i> Tambah Destinasi
                        </a>
                    </div>
                </div>

                <!-- Form Pencarian -->
                <form action="{{ route('admin.destinations.index') }}" method="GET" class="bg-light p-3 rounded m-b-20">
                    <div class="row align-items-center">
                        <div class="col-md-6 col-sm-8 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama destinasi, kota, atau negara..." class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-cyan" type="submit"><i class="mdi mdi-magnify"></i> Cari</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-4 text-md-right mt-2 mt-md-0">
                            @if(request('q'))
                                <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-close m-r-5"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Tabel Destinasi -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 100px;" class="text-center">Foto</th>
                                <th>Nama Destinasi</th>
                                <th>Kota / Wilayah</th>
                                <th>Negara</th>
                                <th class="text-center">Paket Terkait</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($destinations as $index => $dest)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">
                                        {{ $destinations->firstItem() + $index }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($dest->thumbnail)
                                            <img src="{{ Storage::url($dest->thumbnail) }}" alt="{{ $dest->name }}" 
                                                 class="rounded border shadow-sm" style="width: 70px; height: 50px; object-fit: cover;"
                                                 onerror="this.src='{{ asset('images/Balii.jpg') }}'">
                                        @else
                                            <img src="{{ asset('images/Balii.jpg') }}" alt="{{ $dest->name }}" 
                                                 class="rounded border shadow-sm" style="width: 70px; height: 50px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td class="align-middle font-weight-bold text-dark font-14">
                                        <a href="{{ route('admin.destinations.show', $dest) }}" class="text-cyan">
                                            {{ $dest->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle">{{ $dest->city }}</td>
                                    <td class="align-middle">
                                        <span class="badge badge-light border text-dark font-12">
                                            {{ $dest->country }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($dest->tour_packages_count > 0)
                                            <span class="badge badge-info font-12">{{ $dest->tour_packages_count }} Paket</span>
                                        @else
                                            <span class="badge badge-secondary font-12">0 Paket</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted font-13" style="max-width: 250px;">
                                        {{ Str::limit($dest->description, 60, '...') ?: '-' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.destinations.show', $dest) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.destinations.edit', $dest) }}" class="btn btn-cyan btn-sm" title="Edit Destinasi">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.destinations.destroy', $dest) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Destinasi">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-map-marker-off font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada destinasi ditemukan</h5>
                                            <p class="mb-3">Tambahkan destinasi baru agar dapat dipilih saat pembuatan paket wisata.</p>
                                            <a href="{{ route('admin.destinations.create') }}" class="btn btn-success waves-effect waves-light">
                                                <i class="mdi mdi-plus m-r-5"></i> Tambah Destinasi Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center m-t-20">
                    <div class="text-muted font-13">
                        Menampilkan {{ $destinations->firstItem() ?? 0 }} - {{ $destinations->lastItem() ?? 0 }} dari {{ $destinations->total() }} destinasi
                    </div>
                    <div>
                        {{ $destinations->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

