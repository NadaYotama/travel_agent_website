@extends('layouts.app')

@section('title', 'Riwayat Pesanan - ' . config('app.name', 'Terra Travel'))

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-[var(--lagoon)]">Akun customer</p>
            <h1 class="mt-1 font-display text-2xl font-extrabold text-[var(--ink)] sm:text-3xl">Riwayat Pesanan</h1>
            <p class="mt-2 text-sm text-slate-600">Periksa status, detail, dan pembayaran semua pesanan Anda.</p>
        </div>
        <a href="{{ route('user.home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--lagoon)] hover:underline">
            &larr; Kembali ke dashboard
        </a>
    </div>

    <nav class="mb-6 flex gap-2 overflow-x-auto pb-1" aria-label="Filter status pesanan">
        <a href="{{ route('user.bookings.index') }}"
           class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ $selectedStatus === null ? 'bg-[var(--ink)] text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            Semua
        </a>
        @foreach ($statuses as $status)
            <a href="{{ route('user.bookings.index', ['status' => $status->value]) }}"
               class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ $selectedStatus === $status ? 'bg-[var(--ink)] text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                {{ match ($status) {
                    \App\Enums\BookingStatus::PENDING => 'Menunggu bayar',
                    \App\Enums\BookingStatus::PAID => 'Verifikasi',
                    \App\Enums\BookingStatus::CONFIRMED => 'Terkonfirmasi',
                    \App\Enums\BookingStatus::COMPLETED => 'Selesai',
                    \App\Enums\BookingStatus::CANCELLED => 'Dibatalkan',
                } }}
            </a>
        @endforeach
    </nav>

    @if ($bookings->isNotEmpty())
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                @php
                    [$statusLabel, $statusClass] = match ($booking->status) {
                        \App\Enums\BookingStatus::PENDING => ['Menunggu bayar', 'bg-amber-100 text-amber-800'],
                        \App\Enums\BookingStatus::PAID => ['Menunggu verifikasi', 'bg-sky-100 text-sky-800'],
                        \App\Enums\BookingStatus::CONFIRMED => ['Terkonfirmasi', 'bg-emerald-100 text-emerald-800'],
                        \App\Enums\BookingStatus::COMPLETED => ['Selesai', 'bg-indigo-100 text-indigo-800'],
                        \App\Enums\BookingStatus::CANCELLED => ['Dibatalkan', 'bg-rose-100 text-rose-800'],
                    };
                @endphp
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $booking->code }}</span>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>
                            <h2 class="mt-2 font-display text-lg font-bold text-[var(--ink)]">
                                {{ $booking->tourPackage->title ?? 'Paket wisata' }}
                            </h2>
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-500">
                                <span>Dipesan {{ $booking->created_at->format('d M Y') }}</span>
                                <span>{{ $booking->total_participants }} peserta</span>
                                @if ($booking->tourPackage?->start_date)
                                    <span>Berangkat {{ $booking->tourPackage->start_date->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-col gap-2 sm:items-end">
                            <p class="font-display text-lg font-extrabold text-[var(--ink)]">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </p>
                            @if ($booking->status === \App\Enums\BookingStatus::PENDING)
                                <a href="{{ route('user.payments.create', $booking->code) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[var(--lagoon)] px-4 py-2 text-sm font-bold text-white transition hover:bg-[var(--lagoon-dark)]">
                                    Lanjutkan pembayaran
                                </a>
                            @else
                                <a href="{{ route('user.bookings.show', $booking->code) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                    Lihat detail
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400" aria-hidden="true">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8m-8 4h8m-8 4h4M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                </svg>
            </div>
            <h2 class="mt-4 font-display text-lg font-bold text-[var(--ink)]">Belum ada pesanan di kategori ini</h2>
            <p class="mt-2 text-sm text-slate-500">Pesanan yang sesuai filter akan tampil di sini.</p>
            @if ($selectedStatus)
                <a href="{{ route('user.bookings.index') }}" class="mt-4 inline-flex rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Tampilkan semua pesanan
                </a>
            @else
                <a href="{{ route('user.packages.index') }}" class="mt-4 inline-flex rounded-xl bg-[var(--lagoon)] px-4 py-2 text-sm font-semibold text-white hover:bg-[var(--lagoon-dark)]">
                    Jelajahi paket wisata
                </a>
            @endif
        </div>
    @endif
</section>
@endsection
