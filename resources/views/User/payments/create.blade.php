@extends('layouts.app')

@section('title', 'Pembayaran Pesanan - ' . config('app.name', 'Terra Travel'))

@section('content')
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <a href="{{ route('user.packages.show', $booking->tourPackage->slug) }}" class="text-sm font-semibold text-[var(--lagoon)] hover:underline">
            &larr; Kembali ke paket
        </a>
        <h1 class="mt-5 font-display text-2xl font-extrabold text-[var(--ink)] sm:text-3xl">Selesaikan pembayaran</h1>
        <p class="mt-2 text-sm text-slate-600">Pesanan <strong>{{ $booking->code }}</strong> untuk {{ $booking->tourPackage->title }}.</p>

        <div class="mt-6 rounded-2xl bg-teal-50 p-5">
            <div class="flex items-center justify-between gap-4 text-sm">
                <span class="text-slate-600">Total pembayaran ({{ $booking->total_participants }} peserta)</span>
                <strong class="text-lg text-[var(--ink)]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
            </div>
        </div>

        @if ($midtransAvailable)
            <div class="mt-7">
                <button type="button" id="pay-with-midtrans" class="w-full rounded-xl bg-[var(--lagoon)] px-6 py-3.5 font-bold text-white shadow-md transition hover:bg-[var(--lagoon-dark)] disabled:cursor-wait disabled:opacity-60">
                    Bayar dengan Midtrans
                </button>
                <p id="midtrans-message" class="mt-3 text-center text-sm text-slate-600" role="status" aria-live="polite"></p>
                <p class="mt-2 text-center text-xs text-slate-500">Pembayaran diproses dengan aman oleh Midtrans.</p>
            </div>
        @else
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900" role="status">
                Pembayaran online belum tersedia. Administrator perlu mengatur kredensial Midtrans sebelum transaksi dapat diproses.
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

    </div>
</section>

@if ($midtransAvailable)
    <script src="{{ $midtransProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        (() => {
            const button = document.getElementById('pay-with-midtrans');
            const message = document.getElementById('midtrans-message');

            button.addEventListener('click', async () => {
                if (!window.snap) {
                    message.textContent = 'Layanan pembayaran Midtrans tidak dapat dimuat. Periksa koneksi internet lalu coba lagi.';
                    return;
                }

                button.disabled = true;
                message.textContent = 'Menyiapkan pembayaran...';

                try {
                    const response = await fetch(@json(route('user.payments.midtrans', $booking->code)), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Pembayaran tidak dapat disiapkan. Silakan coba lagi.');
                    }

                    window.snap.pay(data.snap_token, {
                        onSuccess: () => window.location.assign(@json(route('user.bookings.show', $booking->code)) + '?payment=success'),
                        onPending: () => window.location.assign(@json(route('user.bookings.show', $booking->code)) + '?payment=pending'),
                        onError: () => {
                            message.textContent = 'Pembayaran gagal diproses. Silakan coba lagi.';
                            button.disabled = false;
                        },
                        onClose: () => {
                            message.textContent = 'Jendela pembayaran ditutup. Anda dapat melanjutkan pembayaran kapan saja.';
                            button.disabled = false;
                        },
                    });
                } catch (error) {
                    message.textContent = error.message;
                    button.disabled = false;
                }
            });
        })();
    </script>
@endif
@endsection
