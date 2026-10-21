@extends('layouts.app')

@section('title', 'Katalog Paket Wisata - Nusa Jelajah')

@section('content')
{{-- Hero / Banner --}}
<div class="bg-gradient-to-r from-[var(--ink)] to-[var(--lagoon)] text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
            Eksplorasi Destinasi
        </span>
        <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">
            Katalog Paket Wisata
        </h1>
        <p class="mt-3 text-lg text-white/80 max-w-2xl">
            Temukan berbagai pilihan liburan terbaik dengan rencana perjalanan terstruktur, fasilitas lengkap, dan jaminan keamanan transaksi.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Search & Filter Section --}}
    <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 mb-8">
        <form action="{{ route('user.packages.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label for="q" class="block text-xs font-semibold text-slate-700 mb-1">Cari Kata Kunci</label>
                <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Judul paket / kota..."
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
            </div>

            <div>
                <label for="destination_id" class="block text-xs font-semibold text-slate-700 mb-1">Destinasi</label>
                <select name="destination_id" id="destination_id"
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
                    <option value="">-- Semua Destinasi --</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>
                            {{ $dest->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-xs font-semibold text-slate-700 mb-1">Bulan Keberangkatan</label>
                <input type="month" name="bulan" id="bulan" value="{{ request('bulan') }}"
                       class="w-full text-sm rounded-xl border-slate-200 focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
            </div>

            <div>
                <label for="sort" class="block text-xs font-semibold text-slate-700 mb-1">Urutkan</label>
                <select name="sort" id="sort"
                        class="w-full text-sm rounded-xl border-slate-200 focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
                    <option value="terdekat" {{ request('sort') == 'terdekat' ? 'selected' : '' }}>Jadwal Terdekat</option>
                    <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                    <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-[var(--ink)] hover:bg-[var(--lagoon)] text-white text-sm font-semibold rounded-xl transition duration-150 shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['q', 'destination_id', 'bulan', 'peserta', 'sort']))
                    <a href="{{ route('user.packages.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition" title="Reset filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Package Grid --}}
    @if($packages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($packages as $pkg)
                <article class="flex flex-col bg-white rounded-3xl overflow-hidden shadow-card border border-slate-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    {{-- Cover Image --}}
                    <div class="relative h-56 bg-slate-100 overflow-hidden">
                        <img src="{{ $pkg->cover_url ?? asset('images/placeholder.svg') }}" alt="{{ $pkg->title }}"
                             class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                             onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';">

                        {{-- Destinasi Badge --}}
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            @if($pkg->destination)
                                <span class="bg-[var(--ink)]/80 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    📍 {{ $pkg->destination->name }}
                                </span>
                            @endif
                            <span class="bg-[var(--sun)] text-[var(--ink)] text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                ⏱️ {{ $pkg->duration_days }} Hari
                            </span>
                        </div>

                        {{-- Sisa Kuota --}}
                        <div class="absolute bottom-3 right-3">
                            <span class="bg-black/60 backdrop-blur-md text-white text-xs px-2.5 py-1 rounded-lg">
                                Kuota: <strong class="text-[var(--sun)]">{{ $pkg->quota }}</strong> kursi
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Mulai {{ optional($pkg->start_date)->format('d M Y') }}</span>
                            @if($pkg->reviews_avg_rating)
                                <span class="ml-auto flex items-center gap-1 font-semibold text-amber-500">
                                    ★ {{ number_format($pkg->reviews_avg_rating, 1) }}
                                </span>
                            @endif
                        </div>

                        <h3 class="font-display text-lg font-bold text-slate-800 line-clamp-2 hover:text-[var(--lagoon)] transition">
                            <a href="{{ route('user.packages.show', $pkg->slug) }}">{{ $pkg->title }}</a>
                        </h3>

                        <p class="mt-2 text-sm text-slate-600 line-clamp-2">
                            {{ $pkg->description }}
                        </p>

                        <div class="mt-auto pt-6 border-t border-slate-100 flex items-end justify-between">
                            <div>
                                <span class="text-xs text-slate-500 block">Mulai dari</span>
                                <span class="font-display text-xl font-extrabold text-[var(--lagoon-dark)]">
                                    Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-500">/ orang</span>
                            </div>

                            <a href="{{ route('user.packages.show', $pkg->slug) }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-[var(--lagoon)] hover:bg-[var(--lagoon-dark)] text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                <span>Detail</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $packages->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center shadow-card border border-slate-100">
            <div class="w-16 h-16 mx-auto bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-3xl mb-4">
                🏖️
            </div>
            <h3 class="font-display text-xl font-bold text-slate-800 mb-2">Tidak ada paket wisata ditemukan</h3>
            <p class="text-sm text-slate-600 max-w-md mx-auto mb-6">
                Coba ubah kata kunci pencarian, pilih destinasi lain, atau kosongkan filter untuk melihat semua paket yang tersedia.
            </p>
            <a href="{{ route('user.packages.index') }}" class="inline-flex items-center px-5 py-2.5 bg-[var(--lagoon)] text-white text-sm font-semibold rounded-xl hover:bg-[var(--lagoon-dark)] transition">
                Reset Semua Filter
            </a>
        </div>
    @endif
</div>
@endsection
