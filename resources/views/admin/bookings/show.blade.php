@extends('layouts.admin')

@section('title', 'Detail Booking: ' . $booking->code)
@section('header', 'Detail Booking')

@section('content')
<div class="row">
    <!-- Kolom Kiri: Rincian Paket, Peserta, dan Pembayaran -->
    <div class="col-lg-8 col-md-12">
        <!-- Rincian Paket Wisata -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-15 border-bottom p-b-15">
                    <div>
                        <h4 class="card-title mb-0">Paket Wisata</h4>
                        <span class="text-muted font-12">Informasi paket liburan yang dipesan customer.</span>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm waves-effect">
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
                            <div class="d-flex flex-wrap font-13 text-secondary">
                                <span class="m-r-15"><i class="mdi mdi-calendar m-r-5"></i>Mulai: <strong>{{ optional($booking->tourPackage->start_date)->format('d M Y') }}</strong></span>
                                <span class="m-r-15"><i class="mdi mdi-calendar-check m-r-5"></i>Selesai: <strong>{{ optional($booking->tourPackage->end_date)->format('d M Y') }}</strong></span>
                                <span><i class="mdi mdi-clock-outline m-r-5"></i>Durasi: <strong>{{ $booking->tourPackage->duration_days }} Hari</strong></span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <div class="font-12 text-muted">Harga Dasar per Orang</div>
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

        <!-- Riwayat Pembayaran -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title m-b-15">Rincian Pembayaran & Bukti Transfer</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Metode</th>
                                <th>Jumlah Bayar</th>
                                <th>Waktu Bayar</th>
                                <th class="text-center">Bukti Bayar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 170px;">Aksi</th>
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
                                        @if($pStatus === 'pending' && Route::has('admin.payments.verify'))
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi dan setujui pembayaran ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision" value="approve">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui Pembayaran">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Tolak pembayaran ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision" value="reject">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak Pembayaran">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted font-12">-</span>
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

    <!-- Kolom Kanan: Status & Form Update Booking Admin -->
    <div class="col-lg-4 col-md-12">
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

                <ul class="list-group list-group-flush font-14 mb-4">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Tanggal Pemesanan:</span>
                        <strong class="text-dark">{{ optional($booking->created_at)->format('d M Y H:i') ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Customer:</span>
                        <strong class="text-dark">{{ $booking->user->name ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Email:</span>
                        <span>{{ $booking->user->email ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">No. Telepon:</span>
                        <span>{{ $booking->user->phone ?? '-' }}</span>
                    </li>
                    @if($booking->promo)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Promo:</span>
                            <span class="badge badge-success">{{ $booking->promo->code }}</span>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between px-0 bg-light p-3 rounded mt-2">
                        <span class="font-weight-bold text-dark font-16">Total Tagihan:</span>
                        <span class="font-weight-bold text-success font-18">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </li>
                </ul>

                <!-- Form Update Booking untuk Admin -->
                <h5 class="font-weight-bold text-dark font-14 m-b-15 border-top p-t-15">
                    <i class="mdi mdi-settings m-r-5"></i> Kelola Status & Agen PIC
                </h5>

                <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="status" class="font-weight-bold font-13">Status Reservasi <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" {{ in_array($bStatus, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ $bStatus === $st->value ? 'selected' : '' }}>
                                    {{ ucfirst($st->value) }}
                                </option>
                            @endforeach
                        </select>
                        @if(in_array($bStatus, ['completed', 'cancelled']))
                            <small class="text-muted">Status final tidak dapat diubah lagi.</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="agent_id" class="font-weight-bold font-13">Pilih Agen PIC</label>
                        <select name="agent_id" id="agent_id" class="form-control">
                            <option value="">-- Belum Ada PIC --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ $booking->agent_id == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="font-weight-bold font-13">Catatan Admin / Customer</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Tambahkan catatan internal...">{{ old('notes', $booking->notes) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-cyan btn-block font-weight-bold waves-effect waves-light">
                        <i class="mdi mdi-content-save m-r-5"></i> Simpan Perubahan Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

