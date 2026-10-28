@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')
@section('header', 'Pembayaran')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block align-items-center m-b-20">
                    <div>
                        <h4 class="card-title mb-0">Daftar Transaksi Pembayaran</h4>
                        <p class="text-muted font-13 mb-0">Status pembayaran Midtrans diperbarui otomatis melalui notifikasi resmi gateway.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Kode Booking</th>
                                <th>Customer</th>
                                <th>Metode Bayar</th>
                                <th>Jumlah Tagihan</th>
                                <th>Waktu Bayar</th>
                                <th class="text-center">Bukti Transfer</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Refund</th>
                                <th class="text-center" style="min-width: 280px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $idx => $p)
                                @php
                                    $pStatus = is_object($p->status) ? $p->status->value : $p->status;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle font-weight-bold">
                                        {{ $payments->firstItem() + $idx }}
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        @if($p->booking)
                                            <a href="{{ route('admin.bookings.show', $p->booking) }}" class="text-cyan">
                                                {{ $p->booking->code }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="align-middle">{{ $p->booking->user->name ?? '-' }}</td>
                                    <td class="align-middle text-uppercase font-weight-bold">{{ $p->method }}</td>
                                    <td class="align-middle font-weight-bold text-success font-14">
                                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle font-12">
                                        {{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($p->proof)
                                            <a href="{{ Storage::url($p->proof) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="mdi mdi-image m-r-5"></i> Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-muted font-12">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
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
                                    <td class="align-middle">
                                        @if($p->refund_status === \App\Enums\RefundStatus::REQUESTED->value)
                                            <span class="badge badge-warning text-dark p-2">Menunggu diproses</span>
                                            <p class="mt-2 mb-1 text-left font-12">{{ $p->refund_reason }}</p>
                                        @elseif($p->refund_status === \App\Enums\RefundStatus::PROCESSED->value)
                                            <span class="badge badge-success p-2">Diproses</span>
                                            <p class="mt-1 mb-0 font-12">{{ $p->refund_note }}</p>
                                        @elseif($p->refund_status === \App\Enums\RefundStatus::REJECTED->value)
                                            <span class="badge badge-danger p-2">Ditolak</span>
                                            <p class="mt-1 mb-0 font-12">{{ $p->refund_note }}</p>
                                        @else
                                            <span class="text-muted font-12">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($p->booking)
                                            <a href="{{ route('admin.bookings.show', $p->booking) }}" class="btn btn-outline-info btn-sm mb-2">
                                                <i class="mdi mdi-account-card-details"></i> Detail customer
                                            </a>
                                        @endif

                                        @if($p->refund_status === \App\Enums\RefundStatus::REQUESTED->value)
                                            <form action="{{ route('admin.payments.refund', $p) }}" method="POST" class="mb-2">
                                                @csrf
                                                @method('PATCH')
                                                <label for="refund-note-{{ $p->id }}" class="mb-1 d-block font-12">Catatan untuk customer</label>
                                                <textarea id="refund-note-{{ $p->id }}" name="refund_note" maxlength="1000" required rows="2" class="form-control form-control-sm mb-2" placeholder="Tulis alasan penolakan atau referensi refund"></textarea>
                                                <div class="d-flex flex-wrap" style="gap: 4px;">
                                                    <button type="submit" name="decision" value="processed" class="btn btn-success btn-sm" onclick="return confirm('Pastikan dana refund sudah benar-benar dikembalikan. Status selesai akan terlihat oleh customer.');">
                                                        Tandai refund selesai
                                                    </button>
                                                    <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tolak permintaan refund dan kirim catatan ini ke customer?');">
                                                        Tolak permintaan
                                                    </button>
                                                </div>
                                            </form>
                                        @endif

                                        @if($pStatus === 'pending' && $p->method !== 'midtrans_snap' && Route::has('admin.payments.verify'))
                                            <div class="btn-group mb-2" role="group">
                                                <form action="{{ route('admin.payments.verify', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui pembayaran ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision" value="approve">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.payments.verify', $p) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Tolak pembayaran ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision" value="reject">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($p->refund_status !== \App\Enums\RefundStatus::REQUESTED->value && ! $p->booking)
                                            <span class="text-muted font-12">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-credit-card-off font-48 d-block mb-2 text-warning"></i>
                                            <h5>Belum ada transaksi pembayaran</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center m-t-20">
                    <div class="text-muted font-13">
                        Menampilkan {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }} transaksi
                    </div>
                    <div>
                        {{ $payments->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
