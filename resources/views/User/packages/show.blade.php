@extends('layouts.app')

@section('title', $package->title . ' - Nusa Jelajah')

@section('content')
<div class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        {{-- Breadcrumbs --}}
        <nav class="flex text-sm text-slate-500 gap-2 items-center">
            <a href="{{ url('/') }}" class="hover:text-[var(--lagoon)]">Beranda</a>
            <span>/</span>
            <a href="{{ route('user.packages.index') }}" class="hover:text-[var(--lagoon)]">Paket Wisata</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md">{{ $package->title }}</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Detail Paket, Galeri, Itinerary, Review --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Header Judul & Badge --}}
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if($package->destination)
                        <span class="bg-[var(--ink)] text-white text-xs font-semibold px-3 py-1 rounded-full">
                            📍 {{ $package->destination->name }}, {{ $package->destination->country ?? 'Indonesia' }}
                        </span>
                    @endif
                    <span class="bg-[var(--sun)] text-[var(--ink)] text-xs font-bold px-3 py-1 rounded-full">
                        ⏱️ {{ $package->duration_days }} Hari
                    </span>
                    @if($package->reviews_avg_rating)
                        <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                            ★ {{ number_format($package->reviews_avg_rating, 1) }} ({{ $package->reviews_count }} ulasan)
                        </span>
                    @endif
                </div>
                <h1 class="font-display text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $package->title }}
                </h1>
            </div>

            {{-- Galeri Foto --}}
            <div class="bg-white rounded-3xl overflow-hidden shadow-card border border-slate-100 p-2">
                <div class="relative h-80 sm:h-96 w-full rounded-2xl overflow-hidden bg-slate-100">
                    @if($package->images->first())
                        <img src="{{ Storage::url($package->images->first()->path) }}" alt="{{ $package->title }}"
                             id="main-preview" class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='{{ asset('images/Balii.jpg') }}';">
                    @else
                        <img src="{{ asset('images/Balii.jpg') }}" alt="{{ $package->title }}"
                             id="main-preview" class="w-full h-full object-cover">
                    @endif
                </div>

                @if($package->images->count() > 1)
                    <div class="flex gap-2 overflow-x-auto p-2 mt-2">
                        @foreach($package->images as $img)
                            <button type="button" 
                                    onclick="document.getElementById('main-preview').src = '{{ Storage::url($img->path) }}'"
                                    class="w-20 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-[var(--lagoon)] flex-shrink-0 transition">
                                <img src="{{ Storage::url($img->path) }}" class="w-full h-full object-cover" alt="Thumbnail">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Deskripsi Paket --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-slate-100">
                <h2 class="font-display text-xl font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">
                    Tentang Paket Wisata
                </h2>
                <div class="text-slate-600 leading-relaxed text-sm sm:text-base space-y-4">
                    {!! nl2br(e($package->description)) !!}
                </div>
            </div>

            {{-- Rencana Perjalanan (Itinerary) --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-slate-100">
                <h2 class="font-display text-xl font-bold text-slate-900 mb-6 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <span>🗓️ Rencana Perjalanan (Itinerary)</span>
                    <span class="text-xs font-normal text-slate-500">{{ $package->itineraries->count() }} Hari Kegiatan</span>
                </h2>

                @if($package->itineraries->count() > 0)
                    <div class="space-y-6 relative before:absolute before:inset-0 before:left-3 before:w-0.5 before:bg-slate-200">
                        @foreach($package->itineraries as $itinerary)
                            <div class="relative flex items-start gap-4 pl-8">
                                <div class="absolute left-0 top-1 w-6.5 h-6.5 rounded-full bg-[var(--lagoon)] text-white text-xs font-bold flex items-center justify-center ring-4 ring-white">
                                    {{ $itinerary->day }}
                                </div>
                                <div class="flex-1 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                    <h3 class="font-display font-bold text-slate-800 text-base">
                                        Hari {{ $itinerary->day }}: {{ $itinerary->title }}
                                    </h3>
                                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                        {{ $itinerary->description }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 text-sm italic">Jadwal itinerary detail akan dikonfirmasikan oleh agen setelah pemesanan.</p>
                @endif
            </div>

            {{-- Ulasan Pelanggan --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-slate-100">
                <h2 class="font-display text-xl font-bold text-slate-900 mb-6 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <span>⭐ Ulasan Traveler</span>
                    @if($package->reviews_count > 0)
                        <span class="text-sm text-slate-500 font-normal">{{ $package->reviews_count }} Ulasan</span>
                    @endif
                </h2>

                @if($package->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($package->reviews as $review)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-[var(--ink)] text-white text-xs font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-sm text-slate-800">{{ $review->user->name ?? 'Traveler' }}</span>
                                    </div>
                                    <div class="text-amber-500 text-sm">
                                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                    </div>
                                </div>
                                <p class="text-sm text-slate-600 mt-1">{{ $review->comment }}</p>
                                <span class="text-xs text-slate-400 block mt-2">{{ optional($review->created_at)->format('d M Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-500 text-sm">
                        Belum ada ulasan untuk paket ini. Jadilah yang pertama memberikan ulasan!
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Card Booking & Informasi Harga --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100">
                    <div class="mb-6 pb-6 border-b border-slate-100">
                        <span class="text-xs text-slate-500 uppercase tracking-wider font-semibold block">Harga per Peserta</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="font-display text-3xl font-extrabold text-[var(--lagoon-dark)]">
                                Rp {{ number_format($package->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Informasi Keberangkatan --}}
                    <div class="space-y-3 mb-6 text-sm text-slate-600">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span>Mulai:</span>
                            <strong class="text-slate-800">{{ optional($package->start_date)->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span>Selesai:</span>
                            <strong class="text-slate-800">{{ optional($package->end_date)->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span>Durasi:</span>
                            <strong class="text-slate-800">{{ $package->duration_days }} Hari Liburan</strong>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Sisa Kuota:</span>
                            <strong class="{{ $package->quota > 5 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $package->quota }} Peserta
                            </strong>
                        </div>
                    </div>

                    {{-- Tombol Pemesanan --}}
                    <div>
                        @if($package->is_bookable)
                            @auth
                                @if(Route::has('user.bookings.create'))
                                    <a href="{{ route('user.bookings.create', $package) }}" 
                                       class="w-full py-3 px-6 bg-[var(--sun)] hover:bg-[var(--sun-hover)] text-[var(--ink)] font-bold text-base rounded-2xl transition shadow-md flex items-center justify-center gap-2">
                                        <span>Pesan Paket Sekarang</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                @else
                                    <button class="w-full py-3 px-6 bg-[var(--sun)] text-[var(--ink)] font-bold text-base rounded-2xl shadow-md">
                                        Hubungi Agen Kami
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" 
                                   class="w-full py-3 px-6 bg-[var(--lagoon)] hover:bg-[var(--lagoon-dark)] text-white font-bold text-base rounded-2xl transition shadow-md flex items-center justify-center gap-2">
                                    <span>Masuk untuk Memesan</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                </a>
                            @endauth
                        @else
                            <button disabled class="w-full py-3 px-6 bg-slate-200 text-slate-500 font-semibold text-base rounded-2xl cursor-not-allowed text-center">
                                Kuota Habis / Jadwal Lewat
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Paket Terkait --}}
                @if(isset($related) && $related->count() > 0)
                    <div class="bg-white rounded-3xl p-6 shadow-card border border-slate-100">
                        <h3 class="font-display text-base font-bold text-slate-800 mb-4">
                            Paket Serupa Lainnya
                        </h3>
                        <div class="space-y-4">
                            @foreach($related as $rel)
                                <a href="{{ route('user.packages.show', $rel->slug) }}" class="flex gap-3 group items-center">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                        @if($rel->images->first())
                                            <img src="{{ Storage::url($rel->images->first()->path) }}" class="w-full h-full object-cover group-hover:scale-110 transition" onerror="this.onerror=null; this.src='{{ asset('images/Balii.jpg') }}';">
                                        @else
                                            <img src="{{ asset('images/Balii.jpg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-slate-800 truncate group-hover:text-[var(--lagoon)] transition">
                                            {{ $rel->title }}
                                        </h4>
                                        <p class="text-xs text-[var(--lagoon-dark)] font-extrabold mt-1">
                                            Rp {{ number_format($rel->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

