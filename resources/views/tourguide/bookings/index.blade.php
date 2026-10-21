@extends('layouts.tourguide')

@section('title', 'Daftar Pemesanan Masuk')
@section('header', 'Pemesanan Masuk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Kelola Pemesanan Paket Wisata</h4>
                        <p class="text-muted font-13 mb-0">Pantau status pembayaran, klaim penanggung jawab paket, dan status perjalanan customer.</p>
                    </div>
                </div>
                <div id="payment-sync-notice" class="alert alert-warning d-none" role="status" aria-live="polite">
                    Status Midtrans belum dapat diperiksa. Pastikan MIDTRANS_SERVER_KEY sesuai dan aplikasi dapat terhubung ke Midtrans.
                </div>

                <!-- Scope Tabs Filter -->
                <ul class="nav nav-tabs m-b-20" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $scope === 'all' ? 'active' : '' }}" href="{{ route('tourguide.bookings.index', array_merge(request()->query(), ['scope' => 'all'])) }}">
                            <i class="mdi mdi-view-list m-r-5"></i> Semua Booking
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $scope === 'mine' ? 'active' : '' }}" href="{{ route('tourguide.bookings.index', array_merge(request()->query(), ['scope' => 'mine'])) }}">
                            <i class="mdi mdi-account-check m-r-5"></i> Ditugaskan ke Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $scope === 'unassigned' ? 'active' : '' }}" href="{{ route('tourguide.bookings.index', array_merge(request()->query(), ['scope' => 'unassigned'])) }}">
                            <i class="mdi mdi-account-alert m-r-5"></i> Belum Ada Tour Guide (Perlu Diklaim)
                        </a>
                    </li>
                </ul>

                <!-- Filter & Search Bar -->
                <form action="{{ route('tourguide.bookings.index') }}" method="GET" class="bg-light p-3 rounded m-b-20">
                    <input type="hidden" name="scope" value="{{ $scope }}">
                    <div class="row align-items-center">
                        <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Cari Kode Booking / Nama Customer:</label>
                            <div class="input-group">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="TRV-2026... atau nama customer" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="submit"><i class="mdi mdi-magnify"></i> Cari</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark font-12 mb-1">Status Pemesanan:</label>
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
                                <a href="{{ route('tourguide.bookings.index', ['scope' => $scope]) }}" class="btn btn-secondary waves-effect">
                                    <i class="mdi mdi-close m-r-5"></i> Reset
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
                                <th>Tour Guide PIC</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $b)
                                @php
                                    $stVal = is_object($b->status) ? $b->status->value : $b->status;
                                    $hasVerifiedPayment = $b->payments->contains(
                                        fn ($payment) => $payment->status === \App\Enums\PaymentStatus::VERIFIED
                                    );
                                @endphp
                                <tr data-booking-id="{{ $b->id }}">
                                    <td class="align-middle font-weight-bold">
                                        <a href="{{ route('tourguide.bookings.show', $b) }}" class="text-cyan">
                                            {{ $b->code }}
                                        </a>
                                        <div class="font-11 text-muted">{{ optional($b->created_at)->format('d/m/Y H:i') ?? '-' }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark">{{ $b->user->name ?? '-' }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark">{{ $b->tourPackage->title ?? '-' }}</div>
                                        <small class="text-muted">
                                            <i class="mdi mdi-calendar"></i> Berangkat: {{ optional($b->tourPackage->start_date)->format('d M Y') ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="align-middle text-center font-weight-bold">
                                        {{ $b->total_participants }} Orang
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($b->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-center" data-booking-status-cell data-paid="{{ $hasVerifiedPayment ? 'true' : 'false' }}">
                                        @if($hasVerifiedPayment)
                                            <span class="badge badge-success font-12 p-2" data-booking-payment-status data-paid="true">Sudah Dibayar</span>
                                        @elseif($stVal === 'pending')
                                            <span class="badge badge-warning text-dark font-12 p-2">Menunggu Bayar</span>
                                        @elseif($stVal === 'paid')
                                            <span class="badge badge-info font-12 p-2">Menunggu Konfirmasi</span>
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
                                                <i class="mdi mdi-alert-circle m-r-5"></i>Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tourguide.bookings.show', $b) }}" class="btn btn-cyan btn-sm" title="Lihat Detail">
                                                <i class="mdi mdi-eye"></i> Detail
                                            </a>
                                            @if(!$b->agent_id)
                                                <form action="{{ route('tourguide.bookings.claim', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Ambil alih tanggung jawab booking ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm ml-1" title="Klaim Booking">
                                                        <i class="mdi mdi-hand-right"></i> Ambil
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-receipt font-48 d-block mb-2 text-warning"></i>
                                            <h5>Tidak ada data pemesanan ditemukan</h5>
                                            <p class="mb-0">Tidak ada pesanan yang sesuai dengan filter atau kata kunci pencarian Anda.</p>
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
@if($bookings->count())
    @push('scripts')
        <script>
            (() => {
                const bookingIds = @json($bookings->pluck('id')->values());
                const endpoint = @json(route('tourguide.bookings.payment-statuses'));
                const paidBadge = '<span class="badge badge-success font-12 p-2" data-booking-payment-status data-paid="true">Sudah Dibayar</span>';
                let polling = true;

                const refreshPaymentStatuses = async () => {
                    if (!polling) return;

                    try {
                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ booking_ids: bookingIds }),
                            cache: 'no-store',
                        });
                        if (!response.ok) throw new Error(`Status request failed: ${response.status}`);

                        const data = await response.json();
                        const syncNotice = document.getElementById('payment-sync-notice');
                        syncNotice.classList.toggle('d-none', !data.sync_errors?.length);
                        data.bookings.forEach((booking) => {
                            if (!booking.is_paid) return;
                            const row = document.querySelector(`[data-booking-id="${booking.id}"]`);
                            const statusCell = row?.querySelector('[data-booking-status-cell]');
                            if (statusCell && statusCell.dataset.paid !== 'true') {
                                statusCell.innerHTML = paidBadge;
                                statusCell.dataset.paid = 'true';
                            }
                        });
                    } catch (error) {
                        console.error('Tidak dapat memperbarui status pembayaran booking.', error);
                        document.getElementById('payment-sync-notice').classList.remove('d-none');
                    }
                };

                refreshPaymentStatuses();
                window.setInterval(refreshPaymentStatuses, 7000);
            })();
        </script>
    @endpush
@endif
@endsection
