@extends('layouts.vendor')

@section('title', 'Daftar Layanan Saya')
@section('header', 'Layanan Saya')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Kelola Layanan Vendor</h4>
                        <p class="text-muted font-13 mb-0">Daftar layanan seperti hotel, transportasi, dan pemandu wisata yang Anda sediakan.</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('vendor.services.create') }}" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-plus m-r-5"></i> Tambah Layanan
                        </a>
                    </div>
                </div>

                <!-- Filter Jenis Layanan -->
                <form action="{{ route('vendor.services.index') }}" method="GET" class="row align-items-center m-b-20 bg-light p-3 mx-0 rounded">
                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                        <label class="font-weight-bold text-dark mb-1">Filter Kategori:</label>
                        <select name="type" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Jenis Layanan --</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="font-weight-bold text-dark mb-1">&nbsp;</label>
                        <div>
                            @if(request('type'))
                                <a href="{{ route('vendor.services.index') }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-close m-r-5"></i> Reset Filter
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th>Nama Layanan</th>
                                <th>Jenis</th>
                                <th>Harga Dasar</th>
                                <th class="text-center">Dipakai Paket</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $index => $service)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">
                                        {{ $services->firstItem() + $index }}
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark">{{ $service->name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($service->type === 'hotel')
                                            <span class="badge badge-info p-2"><i class="mdi mdi-hotel m-r-5"></i>Hotel</span>
                                        @elseif($service->type === 'transport')
                                            <span class="badge badge-warning text-dark p-2"><i class="mdi mdi-car m-r-5"></i>Transportasi</span>
                                        @elseif($service->type === 'guide')
                                            <span class="badge badge-success p-2"><i class="mdi mdi-account-star m-r-5"></i>Pemandu Wisata</span>
                                        @else
                                            <span class="badge badge-secondary p-2">{{ $types[$service->type] ?? $service->type }}</span>
                                        @endif
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($service->tour_packages_count > 0)
                                            <span class="badge badge-primary font-12">{{ $service->tour_packages_count }} Paket</span>
                                        @else
                                            <span class="badge badge-secondary font-12">Belum dipakai</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted font-13" style="max-width: 250px;">
                                        {{ Str::limit($service->description, 60, '...') ?: '-' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendor.services.edit', $service) }}" class="btn btn-cyan btn-sm" title="Edit Layanan">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('vendor.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Layanan">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-alert-circle-outline font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada layanan yang ditambahkan</h5>
                                            <p class="mb-3">Silakan tambahkan hotel, transportasi, atau pemandu wisata untuk diintegrasikan ke paket wisata.</p>
                                            <a href="{{ route('vendor.services.create') }}" class="btn btn-success waves-effect waves-light">
                                                <i class="mdi mdi-plus m-r-5"></i> Tambah Layanan Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center m-t-20">
                    <div class="text-muted font-13">
                        Menampilkan {{ $services->firstItem() ?? 0 }} - {{ $services->lastItem() ?? 0 }} dari {{ $services->total() }} layanan
                    </div>
                    <div>
                        {{ $services->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

