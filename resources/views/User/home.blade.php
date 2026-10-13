@extends('layouts.app')

@section('title', 'Beranda Akun')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header Sambutan --}}
    <div class="bg-gradient-to-r from-[var(--ink)] to-[var(--lagoon)] rounded-3xl p-6 sm:p-8 text-white shadow-card mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
                    Customer Panel
                </span>
                <h1 class="font-display text-2xl sm:text-3xl font-extrabold">
                    Halo, {{ Auth::user()->name ?? 'Traveler' }}! 👋
                </h1>
                <p class="text-white/80 text-sm mt-1">
                    Kelola riwayat pesanan, jadwal liburan, dan paket wisata impian Anda di sini.
                </p>
            </div>
            <div>
                <a href="{{ route('user.packages.index') }}" class="btn-sun inline-flex items-center gap-2 font-bold text-sm">
                    <span>Jelajahi Paket Wisata</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Kartu Statistik Pesanan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="stat-card">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pesanan</p>
            <p class="font-display text-3xl font-bold text-slate-800 mt-2">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Menunggu Bayar</p>
            <p class="font-display text-3xl font-bold text-amber-600 mt-2">{{ $stats['awaiting_payment'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Verifikasi</p>
            <p class="font-display text-3xl font-bold text-sky-600 mt-2">{{ $stats['in_verification'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</p>
            <p class="font-display text-3xl font-bold text-emerald-600 mt-2">{{ $stats['completed'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Konten Utama: Perjalanan Mendatang & Riwayat --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Jadwal Perjalanan Mendatang --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <h2 class="font-display text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span>✈️ Perjalanan Mendatang</span>
            </h2>

            @if(isset($upcoming) && $upcoming->count() > 0)
                <div class="space-y-4">
                    @foreach($upcoming as $booking)
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $booking->tourPackage->title ?? 'Paket Wisata' }}</h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    Berangkat: {{ optional($booking->tourPackage->start_date)->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            <span class="badge badge-success">Terkonfirmasi</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <p class="text-slate-500 text-sm">Belum ada perjalanan mendatang yang terkonfirmasi.</p>
                    <a href="{{ route('user.packages.index') }}" class="mt-3 inline-block text-sm font-semibold text-[var(--lagoon)] hover:underline">
                        Cari destinasi sekarang &rarr;
                    </a>
                </div>
            @endif
        </div>

        {{-- Pesanan Terbaru --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <h2 class="font-display text-lg font-bold text-slate-800 mb-4">
                Riwayat Pesanan
            </h2>

            @if(isset($latestBookings) && $latestBookings->count() > 0)
                <div class="space-y-3">
                    @foreach($latestBookings as $b)
                        <div class="p-3 rounded-lg border border-slate-100 text-sm">
                            <p class="font-semibold text-slate-800 truncate">{{ $b->tourPackage->title ?? 'Pesanan' }}</p>
                            <div class="flex items-center justify-between text-xs text-slate-500 mt-1">
                                <span>{{ $b->created_at->format('d/m/Y') }}</span>
                                <span class="capitalize font-medium">{{ $b->status }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-slate-400 text-sm">Belum ada riwayat pemesanan.</p>
            @endif
        </div>
    </div>
</div>
@endsection
