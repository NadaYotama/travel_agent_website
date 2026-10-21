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

        <div class="mt-7 flex flex-wrap gap-3">
            @if ($booking->status === \App\Enums\BookingStatus::PENDING)
                <a href="{{ route('user.payments.create', $booking->code) }}" class="rounded-xl bg-[var(--lagoon)] px-5 py-3 text-sm font-bold text-white hover:bg-[var(--lagoon-dark)]">
                    Lanjutkan pembayaran
                </a>
                <form method="POST" action="{{ route('user.bookings.cancel', $booking->code) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-red-200 px-5 py-3 text-sm font-semibold text-red-700 hover:bg-red-50">
                        Batalkan pesanan
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
