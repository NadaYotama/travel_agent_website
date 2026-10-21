@extends('layouts.tourguide')

@section('title', 'Detail Booking: ' . $booking->code)
@section('header', 'Detail Booking')

@section('content')
<div class="row">
    <!-- Kolom Kiri: Detail Pesanan, Paket, dan Peserta -->
    <div class="col-lg-8 col-md-12">
        <!-- Informasi Paket Wisata -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-15 border-bottom p-b-15">
                    <div>
                        <h4 class="card-title mb-0">Paket Wisata</h4>
                        <span class="text-muted font-12">Detail paket yang dipesan oleh customer.</span>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('tourguide.bookings.index') }}" class="btn btn-secondary btn-sm waves-effect">
                            <i class="mdi mdi-arrow-left m-r-5"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                @if($booking->tourPackage)
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="font-weight-bold text-dark mb-1">{{ $booking->tourPackage->title }}</h4>
                            <p class="text-muted mb-2">
                                <i class="mdi mdi-map-marker text-danger"></i> {{ $booking->tourPackage->destination->name ?? 'Destinasi' }}, {{ $booking->tourPackage->destination->country ?? 'Indonesia' }}
                            </p>
                            <div class="d-flex flex-wrap font-13 text-secondary gap-3">
                                <span class="m-r-15"><i class="mdi mdi-calendar m-r-5"></i>Mulai: <strong>{{ optional($booking->tourPackage->start_date)->format('d M Y') }}</strong></span>
                                <span class="m-r-15"><i class="mdi mdi-calendar-check m-r-5"></i>Selesai: <strong>{{ optional($booking->tourPackage->end_date)->format('d M Y') }}</strong></span>
                                <span><i class="mdi mdi-clock-outline m-r-5"></i>Durasi: <strong>{{ $booking->tourPackage->duration_days }} Hari</strong></span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <div class="font-12 text-muted">Harga Dasar Paket</div>
                            <h4 class="font-weight-bold text-dark">Rp {{ number_format($booking->tourPackage->price, 0, ',', '.') }}</h4>
                            <span class="badge badge-info p-2">{{ $booking->total_participants }} Peserta Terdaftar</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Daftar Peserta -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-15">Daftar Peserta Wisata ({{ $booking->participants->count() }})</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Peserta</th>
                                <th>Nomor Identitas (KTP / Paspor)</th>
                                <th>No. Telepon / WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->participants as $idx => $p)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">{{ $idx + 1 }}</td>
                                    <td class="align-middle font-weight-bold text-dark">{{ $p->name }}</td>
                                    <td class="align-middle font-monospace">{{ $p->id_number ?: '-' }}</td>
                                    <td class="align-middle">{{ $p->phone ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada rincian data peserta.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status Pembayaran Midtrans -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-15">Status Pembayaran Midtrans</h4>
                <div id="payment-status-notice" class="alert alert-info d-none" role="status" aria-live="polite"></div>
                <p class="text-muted font-13">Status diperbarui otomatis berdasarkan notifikasi resmi Midtrans. Tidak perlu verifikasi manual.</p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th>Waktu Pembayaran</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->payments as $payment)
                                @php
                                    $pStatus = is_object($payment->status) ? $payment->status->value : $payment->status;
                                    $statusClass = match ($pStatus) {
                                        'verified' => 'badge-success',
                                        'rejected' => 'badge-danger',
                                        default => 'badge-warning text-dark',
                                    };
                                    $statusLabel = match ($pStatus) {
                                        'verified' => 'Terverifikasi Midtrans',
                                        'rejected' => 'Gagal / Dibatalkan',
                                        default => $payment->method === 'midtrans_snap'
                                            ? 'Menunggu pembayaran Midtrans'
                                            : 'Menunggu konfirmasi pembayaran lama',
                                    };
                                @endphp
                                <tr data-payment-id="{{ $payment->id }}" data-payment-method="{{ $payment->method }}">
                                    <td class="align-middle font-weight-bold">
                                        {{ $payment->method === 'midtrans_snap' ? 'Midtrans' : str_replace('_', ' ', ucfirst($payment->method)) }}
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle font-12" data-payment-paid-at>
                                        {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge {{ $statusClass }} p-2" data-payment-status="{{ $pStatus }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi pembayaran Midtrans.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Status Booking & Aksi Tour Guide -->
    <div class="col-lg-4 col-md-12">
        <!-- Kartu Ringkasan Status -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-20 border-bottom p-b-10">Ringkasan Pemesanan</h4>

                <div class="text-center m-b-20">
                    <span class="font-12 text-muted">Kode Booking</span>
                    <h3 class="font-weight-bold text-dark mb-2">{{ $booking->code }}</h3>
                    @php
                        $bStatus = is_object($booking->status) ? $booking->status->value : $booking->status;
                    @endphp
                    @if($bStatus === 'pending')
                        <span id="booking-status-badge" data-status="{{ $bStatus }}" class="badge badge-warning text-dark font-14 p-2 px-3">Menunggu Pembayaran Midtrans</span>
                    @elseif($bStatus === 'paid')
                        <span id="booking-status-badge" data-status="{{ $bStatus }}" class="badge badge-info font-14 p-2 px-3">Menunggu Konfirmasi Midtrans</span>
                    @elseif($bStatus === 'confirmed')
                        <span id="booking-status-badge" data-status="{{ $bStatus }}" class="badge badge-success font-14 p-2 px-3">Booking Terkonfirmasi</span>
                    @elseif($bStatus === 'completed')
                        <span id="booking-status-badge" data-status="{{ $bStatus }}" class="badge badge-primary font-14 p-2 px-3">Perjalanan Selesai</span>
                    @elseif($bStatus === 'cancelled')
                        <span id="booking-status-badge" data-status="{{ $bStatus }}" class="badge badge-danger font-14 p-2 px-3">Booking Dibatalkan</span>
                    @endif
                </div>

                <ul class="list-group list-group-flush font-14">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tanggal Pemesanan:</span>
                        <strong class="text-dark">{{ optional($booking->created_at)->format('d M Y H:i') ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Customer:</span>
                        <strong class="text-dark">{{ $booking->user->name ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Email Customer:</span>
                        <span>{{ $booking->user->email ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Telepon Customer:</span>
                        <span>{{ $booking->user->phone ?? '-' }}</span>
                    </li>
                    @if($booking->promo)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Promo Digunakan:</span>
                            <span class="badge badge-success">{{ $booking->promo->code }}</span>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tour Guide Penanggung Jawab:</span>
                        @if($booking->agent)
                            <strong class="text-cyan"><i class="mdi mdi-account-check m-r-5"></i>{{ $booking->agent->name }}</strong>
                        @else
                            <span class="badge badge-danger">Belum Ditugaskan</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 bg-light p-3 rounded mt-2">
                        <span class="font-weight-bold text-dark font-16">Total Tagihan:</span>
                        <span class="font-weight-bold text-success font-18">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </li>
                </ul>

                @if($booking->notes)
                    <div class="m-t-20 p-3 bg-light rounded">
                        <strong class="font-12 text-muted d-block mb-1">Catatan Khusus Customer:</strong>
                        <p class="font-13 mb-0 text-dark">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Panel Tombol Aksi Tour Guide -->
            <div class="card-footer bg-white border-top">
                <h5 class="font-weight-bold text-dark font-14 m-b-15">Tindakan Tour Guide</h5>

                {{-- Klaim Booking bila belum ada PIC --}}
                @if(!$booking->agent_id)
                    <form action="{{ route('tourguide.bookings.claim', $booking) }}" method="POST" class="m-b-10">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-hand-right m-r-5"></i> Ambil / Klaim Booking Ini
                        </button>
                    </form>
                @endif

                {{-- Selesaikan Booking jika sudah terkonfirmasi dan perjalanan usai --}}
                @if($bStatus === 'confirmed')
                    <form action="{{ route('tourguide.bookings.complete', $booking) }}" method="POST" class="m-b-10" onsubmit="return confirm('Tandai perjalanan ini telah selesai dilaksanakan?');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">
                            <i class="mdi mdi-flag-checkered m-r-5"></i> Tandai Perjalanan Selesai
                        </button>
                    </form>
                @endif

                {{-- Batalkan Booking jika belum selesai/batal --}}
                @if($bStatus !== 'completed' && $bStatus !== 'cancelled')
                    <button type="button" class="btn btn-outline-danger btn-block waves-effect" data-toggle="modal" data-target="#cancelModal">
                        <i class="mdi mdi-close-circle m-r-5"></i> Batalkan Booking
                    </button>

                    <!-- Modal Pembatalan -->
                    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('tourguide.bookings.cancel', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger" id="cancelModalLabel"><i class="mdi mdi-alert"></i> Batalkan Booking</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin membatalkan booking <strong>{{ $booking->code }}</strong>? Kuota paket akan dikembalikan otomatis.</p>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Alasan Pembatalan:</label>
                                            <textarea name="reason" rows="3" class="form-control" placeholder="Contoh: Customer meminta refund / batas waktu pembayaran habis..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-danger font-weight-bold">Ya, Batalkan Booking</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if($booking->payments->contains(fn ($payment) => $payment->method === 'midtrans_snap' && $payment->status === \App\Enums\PaymentStatus::PENDING))
    @push('scripts')
        <script>
            (() => {
                const endpoint = @json(route('tourguide.bookings.payment-status', $booking));
                const notice = document.getElementById('payment-status-notice');
                const bookingBadge = document.getElementById('booking-status-badge');
                const statusLabels = {
                    pending: ['badge-warning text-dark', 'Menunggu pembayaran Midtrans'],
                    verified: ['badge-success', 'Terverifikasi Midtrans'],
                    rejected: ['badge-danger', 'Gagal / Dibatalkan'],
                };
                const bookingLabels = {
                    pending: ['badge-warning text-dark', 'Menunggu Pembayaran Midtrans'],
                    paid: ['badge-info', 'Menunggu Konfirmasi Midtrans'],
                    confirmed: ['badge-success', 'Booking Terkonfirmasi'],
                    completed: ['badge-primary', 'Perjalanan Selesai'],
                    cancelled: ['badge-danger', 'Booking Dibatalkan'],
                };
                let polling = true;
                let hasNotified = false;

                const pollPaymentStatus = async () => {
                    if (!polling) return;

                    try {
                        const response = await fetch(endpoint, {
                            headers: { 'Accept': 'application/json' },
                            cache: 'no-store',
                        });
                        if (!response.ok) throw new Error(`Status request failed: ${response.status}`);

                        const data = await response.json();
                        if (data.sync_errors?.length) {
                            notice.classList.remove('d-none', 'alert-info');
                            notice.classList.add('alert-warning');
                            notice.textContent = 'Status Midtrans belum dapat diperiksa. Pastikan MIDTRANS_SERVER_KEY sesuai dan aplikasi dapat terhubung ke Midtrans.';
                        } else if (!hasNotified) {
                            notice.classList.add('d-none');
                        }
                        let paymentChanged = false;

                        data.payments.forEach((payment) => {
                            const row = document.querySelector(`[data-payment-id="${payment.id}"]`);
                            const badge = row?.querySelector('[data-payment-status]');
                            if (!badge || badge.dataset.paymentStatus === payment.status) return;

                            const [classes, label] = statusLabels[payment.status] || ['badge-secondary', payment.status];
                            badge.className = `badge ${classes} p-2`;
                            badge.textContent = label;
                            badge.dataset.paymentStatus = payment.status;

                            if (payment.paid_at) {
                                const date = new Date(payment.paid_at);
                                row.querySelector('[data-payment-paid-at]').textContent = date.toLocaleString('id-ID');
                            }
                            paymentChanged = true;
                        });

                        const bookingChanged = bookingBadge && bookingBadge.dataset.status !== data.booking_status;
                        if (bookingChanged) {
                            const [classes, label] = bookingLabels[data.booking_status] || ['badge-secondary', data.booking_status];
                            bookingBadge.className = `badge ${classes} font-14 p-2 px-3`;
                            bookingBadge.textContent = label;
                            bookingBadge.dataset.status = data.booking_status;
                        }

                        if ((paymentChanged || bookingChanged) && !hasNotified) {
                            hasNotified = true;
                            notice.classList.remove('d-none', 'alert-info', 'alert-warning', 'alert-success');
                            notice.classList.add(data.booking_status === 'confirmed' ? 'alert-success' : 'alert-warning');
                            notice.textContent = data.booking_status === 'confirmed'
                                ? 'Pembayaran telah dikonfirmasi oleh Midtrans. Booking berhasil dikonfirmasi.'
                                : 'Status pembayaran Midtrans telah diperbarui.';
                            polling = false;
                        }
                    } catch (error) {
                        console.error('Tidak dapat memperbarui status pembayaran Midtrans.', error);
                        notice.classList.remove('d-none', 'alert-info');
                        notice.classList.add('alert-warning');
                        notice.textContent = 'Status pembayaran belum dapat diperbarui otomatis. Muat ulang halaman untuk memeriksa kembali.';
                    }
                };

                pollPaymentStatus();
                window.setInterval(pollPaymentStatus, 7000);
            })();
        </script>
    @endpush
@endif
@endsection
