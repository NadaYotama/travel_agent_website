@extends('layouts.admin')

@section('title', 'Kelola Kode Promo')
@section('header', 'Kode Promo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Daftar Kode Promo</h4>
                        <p class="text-muted font-13 mb-0">Kelola kupon diskon persentase atau nominal untuk pelanggan.</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.promos.create') }}" class="btn btn-success waves-effect waves-light">
                            <i class="mdi mdi-plus m-r-5"></i> Tambah Promo
                        </a>
                    </div>
                </div>

                <!-- Form Cari Promo -->
                <form action="{{ route('admin.promos.index') }}" method="GET" class="bg-light p-3 rounded m-b-20">
                    <div class="row align-items-center">
                        <div class="col-md-6 col-sm-8 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode promo..." class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-cyan" type="submit"><i class="mdi mdi-magnify"></i> Cari</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-4 text-md-right mt-2 mt-md-0">
                            @if(request('q'))
                                <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-close m-r-5"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Tabel Promo -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Kode Promo</th>
                                <th>Jenis Diskon</th>
                                <th>Nilai Diskon</th>
                                <th>Masa Berlaku</th>
                                <th class="text-center">Kuota & Terpakai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promos as $idx => $promo)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">
                                        {{ $promos->firstItem() + $idx }}
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        <span class="badge badge-primary font-14 p-2">{{ $promo->code }}</span>
                                    </td>
                                    <td class="align-middle text-capitalize">
                                        {{ $promo->discount_type === 'percent' ? 'Persentase (%)' : 'Nominal Tetap (Rp)' }}
                                    </td>
                                    <td class="align-middle font-weight-bold text-success font-14">
                                        @if($promo->discount_type === 'percent')
                                            {{ $promo->discount_value }}%
                                        @else
                                            Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="align-middle font-12">
                                        <i class="mdi mdi-calendar-check m-r-5"></i> Berlaku s/d: <strong>{{ optional($promo->valid_until)->format('d/m/Y') ?? '-' }}</strong>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">
                                        <span class="badge badge-light border text-dark font-12">
                                            {{ $promo->bookings_count ?? 0 }} / {{ $promo->quota }} Kuota
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($promo->valid_until && $promo->valid_until->isPast())
                                            <span class="badge badge-danger p-2">Kedaluwarsa</span>
                                        @elseif($promo->quota > 0 && ($promo->bookings_count ?? 0) >= $promo->quota)
                                            <span class="badge badge-warning text-dark p-2">Habis</span>
                                        @else
                                            <span class="badge badge-success p-2">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.promos.edit', $promo) }}" class="btn btn-cyan btn-sm" title="Edit Promo">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kode promo ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Promo">
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
                                            <i class="mdi mdi-tag-off font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada kode promo</h5>
                                            <p class="mb-3">Buat kode diskon baru untuk menarik pemesanan customer.</p>
                                            <a href="{{ route('admin.promos.create') }}" class="btn btn-success waves-effect waves-light">
                                                <i class="mdi mdi-plus m-r-5"></i> Tambah Promo Sekarang
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
                        Menampilkan {{ $promos->firstItem() ?? 0 }} - {{ $promos->lastItem() ?? 0 }} dari {{ $promos->total() }} promo
                    </div>
                    <div>
                        {{ $promos->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
