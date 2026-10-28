@extends('layouts.app')

@section('title', 'Pesanan ' . $booking->code . ' - ' . config('app.name', 'Nusa Jelajah'))

@section('content')
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-[var(--lagoon)]">Detail pesanan</p>
        <h1 class="mt-2 font-display text-2xl font-extrabold text-[var(--ink)] sm:text-3xl">{{ $booking->code }}</h1>
        <p class="mt-2 text-slate-600">{{ $booking->tourPackage->title }}</p>

        @if (request()->query('payment') === 'success')
            <div class="mt-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status">
                Pembayaran berhasil diterima. Status pesanan akan diperbarui setelah konfirmasi Midtrans.
            </div>
        @elseif (request()->query('payment') === 'pending')
            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="status">
                Pembayaran sedang menunggu penyelesaian. Halaman ini akan diperbarui setelah Midtrans mengonfirmasi transaksi.
            </div>
        @endif

        <dl class="mt-7 grid gap-4 border-y border-slate-100 py-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-slate-500">Status</dt>
                <dd class="mt-1 font-semibold capitalize text-slate-800">{{ str_replace('_', ' ', $booking->status->value) }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Jumlah peserta</dt>
                <dd class="mt-1 font-semibold text-slate-800">{{ $booking->total_participants }} orang</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Total harga</dt>
                <dd class="mt-1 font-semibold text-slate-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Tanggal dibuat</dt>
                <dd class="mt-1 font-semibold text-slate-800">{{ $booking->created_at->format('d M Y, H:i') }}</dd>
            </div>
        </dl>

        <h2 class="mt-6 font-display text-lg font-bold text-slate-800">Peserta</h2>
        <ol class="mt-3 space-y-2">
            @foreach ($booking->participants as $participant)
                <li class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $participant->name }}</li>
            @endforeach
        </ol>

        @php
            $refundPayments = $booking->payments->filter(fn ($payment) => $payment->refund_status !== null);
        @endphp
        @if ($refundPayments->isNotEmpty())
            <section class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4" aria-labelledby="refund-heading">
                <h2 id="refund-heading" class="font-display font-bold text-amber-900">Status refund</h2>
                @foreach ($refundPayments as $payment)
                    <div class="mt-2 text-sm text-amber-900">
                        <p>
                            @switch($payment->refund_status)
                                @case('requested')
                                    Permintaan refund sedang menunggu diproses admin.
                                    @break
                                @case('processed')
                                    Refund telah diproses admin.
                                    @break
                                @case('rejected')
                                    Permintaan refund tidak disetujui. Silakan hubungi admin untuk informasi lebih lanjut.
                                    @break
                            @endswitch
                            (Rp {{ number_format($payment->amount, 0, ',', '.') }})
                        </p>
                        @if ($payment->refund_note)
                            <p class="mt-1 text-xs text-amber-800">Catatan admin: {{ $payment->refund_note }}</p>
                        @endif
                    </div>
                @endforeach
                <p class="mt-3 text-xs text-amber-800">Pengajuan refund tidak berarti dana sudah dikembalikan. Admin akan memperbarui status setelah memproses pengembalian dana.</p>
            </section>
        @endif

        <div class="mt-7 flex flex-wrap gap-3">
            @if ($booking->status === \App\Enums\BookingStatus::PENDING)
                <a href="{{ route('user.payments.create', $booking->code) }}" class="rounded-xl bg-[var(--lagoon)] px-5 py-3 text-sm font-bold text-white hover:bg-[var(--lagoon-dark)]">
                    Lanjutkan pembayaran
                </a>
            @endif
            @if (in_array($booking->status, [\App\Enums\BookingStatus::PENDING, \App\Enums\BookingStatus::PAID, \App\Enums\BookingStatus::CONFIRMED], true))
                @php
                    $hasVerifiedPayment = $booking->payments->contains(fn ($payment) => $payment->status === \App\Enums\PaymentStatus::VERIFIED);
                @endphp
                <form method="POST" action="{{ route('user.bookings.cancel', $booking->code) }}" class="w-full space-y-3 rounded-2xl border border-red-100 bg-red-50/50 p-4 sm:w-auto sm:min-w-80" onsubmit="return confirm('{{ $hasVerifiedPayment ? 'Booking akan dibatalkan dan permintaan refund dikirim ke admin. Lanjutkan?' : 'Batalkan pesanan ini?' }}')">
                    @csrf
                    @if ($hasVerifiedPayment)
                        <label for="cancel-reason" class="block text-sm font-semibold text-slate-700">Alasan pembatalan dan refund</label>
                        <textarea id="cancel-reason" name="reason" rows="3" maxlength="500" required
                                  class="w-full rounded-xl border-slate-200 text-sm focus:border-red-400 focus:ring-red-300"
                                  placeholder="Jelaskan alasan pembatalan...">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-sm text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    @endif
                    <button type="submit" class="w-full rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                        {{ $hasVerifiedPayment ? 'Batalkan & ajukan refund' : 'Batalkan pesanan' }}
                    </button>
                </form>
            @endif
            <a href="{{ route('user.packages.show', $booking->tourPackage->slug) }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Lihat paket
            </a>
        </div>
    </div>
</section>
@endsection
