@extends('layouts.agent')

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
                        <a href="{{ route('agent.bookings.index') }}" class="btn btn-secondary btn-sm waves-effect">
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

        <!-- Riwayat & Verifikasi Pembayaran -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-15">Data Pembayaran & Verifikasi Transfer</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Metode</th>
                                <th>Jumlah Tagihan</th>
                                <th>Waktu Bayar</th>
                                <th class="text-center">Bukti Bayar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 170px;">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->payments as $payment)
                                @php
                                    $pStatus = is_object($payment->status) ? $payment->status->value : $payment->status;
                                @endphp
                                <tr>
                                    <td class="align-middle text-uppercase font-weight-bold">{{ $payment->method }}</td>
                                    <td class="align-middle font-weight-bold text-success">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle font-12">
                                        {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($payment->proof)
                                            <a href="{{ Storage::url($payment->proof) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="mdi mdi-image m-r-5"></i> Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-muted font-12">Belum diunggah</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($pStatus === 'paid' || $pStatus === 'verified' || $pStatus === 'success')
                                            <span class="badge badge-success p-2">Lunas / Diverifikasi</span>
                                        @elseif($pStatus === 'pending')
                                            <span class="badge badge-warning text-dark p-2">Menunggu Konfirmasi</span>
                                        @elseif($pStatus === 'failed' || $pStatus === 'rejected')
                                            <span class="badge badge-danger p-2">Ditolak</span>
                                        @else
                                            <span class="badge badge-secondary p-2">{{ ucfirst($pStatus) }}</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($pStatus === 'pending')
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('agent.payments.verify', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi dan setujui pembayaran ini?');">
                                                    @csrf
                                                    <input type="hidden" name="decision" value="approve">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui Pembayaran">
                                                        <i class="mdi mdi-check"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('agent.payments.verify', $payment) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Tolak pembayaran ini dan minta customer kirim ulang bukti?');">
                                                    @csrf
                                                    <input type="hidden" name="decision" value="reject">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak Pembayaran">
                                                        <i class="mdi mdi-close"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted font-12"><i class="mdi mdi-check-all"></i> Terproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Status Booking & Aksi Agen -->
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
                        <span class="badge badge-warning text-dark font-14 p-2 px-3">Menunggu Pembayaran</span>
                    @elseif($bStatus === 'paid')
                        <span class="badge badge-info font-14 p-2 px-3">Menunggu Verifikasi</span>
                    @elseif($bStatus === 'confirmed')
                        <span class="badge badge-success font-14 p-2 px-3">Booking Terkonfirmasi</span>
                    @elseif($bStatus === 'completed')
                        <span class="badge badge-primary font-14 p-2 px-3">Perjalanan Selesai</span>
                    @elseif($bStatus === 'cancelled')
                        <span class="badge badge-danger font-14 p-2 px-3">Booking Dibatalkan</span>
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
                        <span class="text-muted">Agent Penanggung Jawab:</span>
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

            <!-- Panel Tombol Aksi Agen -->
            <div class="card-footer bg-white border-top">
                <h5 class="font-weight-bold text-dark font-14 m-b-15">Tindakan Agen</h5>

                {{-- Klaim Booking bila belum ada PIC --}}
                @if(!$booking->agent_id)
                    <form action="{{ route('agent.bookings.claim', $booking) }}" method="POST" class="m-b-10">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block waves-effect waves-light font-weight-bold">
                            <i class="mdi mdi-hand-right m-r-5"></i> Ambil / Klaim Booking Ini
                        </button>
                    </form>
                @endif

                {{-- Selesaikan Booking jika sudah terkonfirmasi dan perjalanan usai --}}
                @if($bStatus === 'confirmed')
                    <form action="{{ route('agent.bookings.complete', $booking) }}" method="POST" class="m-b-10" onsubmit="return confirm('Tandai perjalanan ini telah selesai dilaksanakan?');">
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
                                <form action="{{ route('agent.bookings.cancel', $booking) }}" method="POST">
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
@endsection
