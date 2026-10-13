@extends('layouts.admin')

@section('title', 'Kelola Pemesanan')
@section('header', 'Pemesanan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Semua Pemesanan Pelanggan</h4>
                        <p class="text-muted font-13 mb-0">Pantau seluruh transaksi pemesanan wisata, atur PIC agen, dan kelola status reservasi.</p>
                    </div>
                </div>

                <!-- Filter & Search Form -->
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="bg-light p-3 rounded m-b-20">
                    <div class="row align-items-center">
                        <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Cari Kode Booking / Nama Customer:</label>
                            <div class="input-group">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="TRV-2026... atau nama customer" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-cyan" type="submit"><i class="mdi mdi-magnify"></i> Cari</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Filter Status Pemesanan:</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Status --</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                        {{ ucfirst($st->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-12 text-md-right mt-2 mt-md-0">
                            @if(request()->hasAny(['q', 'status']))
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-close m-r-5"></i> Reset Filter
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Bookings Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 140px;">Kode Booking</th>
                                <th>Customer</th>
                                <th>Paket Wisata</th>
                                <th class="text-center">Peserta</th>
                                <th>Total Tagihan</th>
                                <th class="text-center">Status</th>
                                <th>Agen PIC</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $b)
                                <tr>
                                    <td class="align-middle font-weight-bold">
                                        <a href="{{ route('admin.bookings.show', $b) }}" class="text-cyan">
                                            {{ $b->code }}
                                        </a>
                                        <div class="font-11 text-muted">{{ optional($b->created_at)->format('d/m/Y H:i') ?? '-' }}</div>
                                    </td>
                                    <td class="align-middle font-weight-bold text-dark">
                                        {{ $b->user->name ?? '-' }}
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark">{{ $b->tourPackage->title ?? '-' }}</span>
                                    </td>
                                    <td class="align-middle text-center font-weight-bold">
                                        {{ $b->total_participants }} Orang
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($b->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @php
                                            $stVal = is_object($b->status) ? $b->status->value : $b->status;
                                        @endphp
                                        @if($stVal === 'pending')
                                            <span class="badge badge-warning text-dark font-12 p-2">Menunggu Bayar</span>
                                        @elseif($stVal === 'paid')
                                            <span class="badge badge-info font-12 p-2">Perlu Verifikasi</span>
                                        @elseif($stVal === 'confirmed')
                                            <span class="badge badge-success font-12 p-2">Terkonfirmasi</span>
                                        @elseif($stVal === 'completed')
                                            <span class="badge badge-primary font-12 p-2">Selesai</span>
                                        @elseif($stVal === 'cancelled')
                                            <span class="badge badge-danger font-12 p-2">Dibatalkan</span>
                                        @else
                                            <span class="badge badge-secondary font-12 p-2">{{ ucfirst($stVal) }}</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($b->agent)
                                            <span class="badge badge-light border text-dark font-12">
                                                <i class="mdi mdi-account-check m-r-5 text-success"></i>{{ $b->agent->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger font-12">
                                                <i class="mdi mdi-alert-circle m-r-5"></i>Belum Ada PIC
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-cyan btn-sm waves-effect" title="Kelola & Detail">
                                            <i class="mdi mdi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-receipt font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada pesanan ditemukan</h5>
                                            <p class="mb-0">Tidak ada data pesanan yang sesuai dengan filter pencarian Anda.</p>
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
                        Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} pesanan
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

