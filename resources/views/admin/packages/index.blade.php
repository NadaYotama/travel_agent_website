@extends('layouts.admin')

@section('title', 'Kelola Paket Wisata')
@section('header', 'Paket Wisata')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Daftar Paket Wisata</h4>
                        <p class="text-muted font-13 mb-0">Kelola katalog paket liburan, kuota peserta, jadwal perjalanan, dan status publikasi.</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-plus m-r-5"></i> Tambah Paket Baru
                        </a>
                    </div>
                </div>

                <!-- Filter & Search Form -->
                <form action="{{ route('admin.packages.index') }}" method="GET" class="bg-light p-3 rounded m-b-20">
                    <div class="row align-items-center">
                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Cari Judul Paket:</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama paket wisata..." class="form-control">
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Destinasi:</label>
                            <select name="destination_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Destinasi --</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>
                                        {{ $dest->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Status Publikasi:</label>
                            <select name="published" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Status --</option>
                                <option value="1" {{ request('published') === '1' ? 'selected' : '' }}>Dipublikasikan</option>
                                <option value="0" {{ request('published') === '0' ? 'selected' : '' }}>Draf (Disembunyikan)</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-6 text-md-right mt-2 mt-md-0">
                            <button type="submit" class="btn btn-cyan waves-effect m-r-5">
                                <i class="mdi mdi-magnify"></i> Cari
                            </button>
                            @if(request()->hasAny(['q', 'destination_id', 'published']))
                                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary waves-effect" title="Reset filter">
                                    <i class="mdi mdi-close"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Packages Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Paket Wisata & Destinasi</th>
                                <th>Jadwal Keberangkatan</th>
                                <th class="text-center">Durasi</th>
                                <th class="text-center">Kuota</th>
                                <th>Harga Dasar</th>
                                <th class="text-center">Booking</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packages as $index => $pkg)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">
                                        {{ $packages->firstItem() + $index }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark font-14">{{ $pkg->title }}</div>
                                        <div class="font-12 text-muted">
                                            <i class="mdi mdi-map-marker text-danger"></i> {{ $pkg->destination->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="align-middle font-12">
                                        <div><i class="mdi mdi-calendar"></i> Mulai: <strong>{{ optional($pkg->start_date)->format('d M Y') }}</strong></div>
                                        <div><i class="mdi mdi-calendar-check"></i> Selesai: <strong>{{ optional($pkg->end_date)->format('d M Y') }}</strong></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-light border text-dark font-12">
                                            {{ $pkg->duration_days }} Hari
                                        </span>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">
                                        <span class="{{ $pkg->quota > 5 ? 'text-success' : 'text-danger' }}">
                                            {{ $pkg->quota }} Kursi
                                        </span>
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($pkg->bookings_count > 0)
                                            <span class="badge badge-info font-12">{{ $pkg->bookings_count }} Pesanan</span>
                                        @else
                                            <span class="badge badge-secondary font-12">0 Pesanan</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($pkg->is_published)
                                            <span class="badge badge-success p-2">Dipublikasikan</span>
                                        @else
                                            <span class="badge badge-warning text-dark p-2">Draf</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.packages.show', $pkg) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="btn btn-cyan btn-sm" title="Edit Paket">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket wisata ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Paket">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-airplane font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada paket wisata ditemukan</h5>
                                            <p class="mb-3">Mulai buat paket wisata baru untuk ditampilkan di katalog pelanggan.</p>
                                            <a href="{{ route('admin.packages.create') }}" class="btn btn-success waves-effect waves-light">
                                                <i class="mdi mdi-plus m-r-5"></i> Tambah Paket Wisata
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
                        Menampilkan {{ $packages->firstItem() ?? 0 }} - {{ $packages->lastItem() ?? 0 }} dari {{ $packages->total() }} paket wisata
                    </div>
                    <div>
                        {{ $packages->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

