<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Terra Travel') }} - Paket Wisata & Perjalanan</title>
    <meta name="description" content="Pesan paket wisata domestik dan internasional dengan itinerary jelas, harga transparan, dan pendamping perjalanan berpengalaman.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #0d2b3a;
            --lagoon: #137b8b;
            --foam: #e8f3f4;
            --sun: #f4b63f;
            --paper: #fbfdfd;
        }
        body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; background: var(--paper); color: var(--ink); }
        .font-display { font-family: 'Bricolage Grotesque', 'Figtree', sans-serif; letter-spacing: -0.02em; }
        .photo { position: relative; overflow: hidden; background: linear-gradient(135deg, #137b8b, #0d2b3a); }
        .photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        :focus-visible { outline: 3px solid var(--sun); outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) { * { scroll-behavior: auto !important; } }
    </style>
</head>
<body class="antialiased" x-data="{ open: false }">

{{-- Data halaman ini disiapkan oleh HomeController@welcome:
     $destinations, $packages, $heroUrl, dan $dashboard. --}}

{{-- ========== NAVBAR ========== --}}
<header class="absolute inset-x-0 top-0 z-30">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Navigasi utama">
        <a href="{{ url('/') }}" class="font-display text-xl font-extrabold text-white">
            {{ config('app.name', 'Nusa Jelajah') }}
        </a>

        <ul class="hidden items-center gap-8 text-sm font-medium text-white/90 md:flex">
            <li><a class="hover:text-white" href="#destinasi">Destinasi</a></li>
            <li><a class="hover:text-white" href="#paket">Paket wisata</a></li>
            <li><a class="hover:text-white" href="#cara-pesan">Cara pesan</a></li>
            <li><a class="hover:text-white" href="#testimoni">Ulasan</a></li>
        </ul>

        <div class="hidden items-center gap-3 md:flex">
            @if ($dashboard)
                <a href="{{ $dashboard }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-[var(--ink)] hover:bg-[var(--foam)]">Buka dashboard</a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-white hover:underline">Masuk</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-full bg-[var(--sun)] px-5 py-2.5 text-sm font-semibold text-[var(--ink)] hover:brightness-95">Daftar gratis</a>
                @endif
            @endif
        </div>

        <button @click="open = !open" class="rounded-lg p-2 text-white md:hidden" :aria-expanded="open" aria-label="Buka menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </nav>

    <div x-show="open" x-cloak @click.outside="open = false" class="mx-5 rounded-2xl bg-white p-5 shadow-xl md:hidden">
        <ul class="space-y-3 text-sm font-medium">
            <li><a href="#destinasi" @click="open = false">Destinasi</a></li>
            <li><a href="#paket" @click="open = false">Paket wisata</a></li>
            <li><a href="#cara-pesan" @click="open = false">Cara pesan</a></li>
            <li><a href="#testimoni" @click="open = false">Ulasan</a></li>
        </ul>
        <div class="mt-5 flex gap-3">
            @if ($dashboard)
                <a href="{{ $dashboard }}" class="w-full rounded-full bg-[var(--ink)] px-5 py-2.5 text-center text-sm font-semibold text-white">Buka dashboard</a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="w-1/2 rounded-full border border-[var(--ink)] px-4 py-2.5 text-center text-sm font-semibold text-[var(--ink)]">Masuk</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-1/2 rounded-full bg-[var(--sun)] px-4 py-2.5 text-center text-sm font-semibold text-[var(--ink)]">Daftar</a>
                @endif
            @endif
        </div>
    </div>
</header>

<main>
{{-- ========== HERO ========== --}}
<section class="photo relative min-h-[640px] text-white">
    <img src="{{ $heroUrl }}" alt="" onerror="this.remove()">
    <div class="absolute inset-0 bg-gradient-to-t from-[#0d2b3a] via-[#0d2b3a]/55 to-[#0d2b3a]/20"></div>

    <div class="relative mx-auto flex min-h-[640px] max-w-7xl flex-col justify-end px-5 pb-28 pt-40 sm:px-8">
        <h1 class="font-display max-w-3xl text-5xl font-extrabold leading-[1.02] sm:text-6xl lg:text-7xl">
            Berangkat dengan rencana yang sudah beres.
        </h1>
        <p class="mt-6 max-w-xl text-lg text-white/85">
            Pilih paket, lihat rencana perjalanan hari per hari, dan bayar dengan aman. Tim agen kami mengurus tiket, hotel, dan transportasi.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="#paket" class="rounded-full bg-[var(--sun)] px-7 py-3.5 text-sm font-semibold text-[var(--ink)] hover:brightness-95">Lihat paket wisata</a>
            <a href="#cara-pesan" class="rounded-full border border-white/60 px-7 py-3.5 text-sm font-semibold hover:bg-white/10">Cara memesan</a>
        </div>
    </div>
</section>

{{-- ========== PENCARIAN (menempel di bawah hero) ========== --}}
<section class="relative z-10 mx-auto -mt-14 max-w-5xl px-5 sm:px-8" aria-label="Cari paket">
    <form action="{{ Route::has('user.packages.index') ? route('user.packages.index') : '#paket' }}" method="GET"
          class="grid gap-4 rounded-3xl bg-white p-5 shadow-2xl shadow-[#0d2b3a]/15 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_auto] lg:items-end">
        <div>
            <label for="q" class="mb-1.5 block text-xs font-semibold text-[var(--lagoon)]">Mau ke mana?</label>
            <input id="q" name="q" type="text" placeholder="Bali, Labuan Bajo, Kyoto..."
                   class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
        </div>
        <div>
            <label for="bulan" class="mb-1.5 block text-xs font-semibold text-[var(--lagoon)]">Bulan berangkat</label>
            <input id="bulan" name="bulan" type="month"
                   class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
        </div>
        <div>
            <label for="peserta" class="mb-1.5 block text-xs font-semibold text-[var(--lagoon)]">Jumlah peserta</label>
            <input id="peserta" name="peserta" type="number" min="1" value="2"
                   class="w-full rounded-xl border-slate-200 text-sm focus:border-[var(--lagoon)] focus:ring-[var(--lagoon)]">
        </div>
        <button type="submit" class="rounded-xl bg-[var(--ink)] px-7 py-3 text-sm font-semibold text-white hover:bg-[var(--lagoon)]">
            Cari paket
        </button>
    </form>
</section>

{{-- ========== DESTINASI ========== --}}
<section id="destinasi" class="mx-auto max-w-7xl px-5 py-24 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h2 class="font-display max-w-lg text-4xl font-bold leading-tight">Destinasi yang paling sering dipesan</h2>
        <p class="max-w-sm text-slate-600">Dari pulau-pulau di Indonesia sampai kota-kota di Asia dan Eropa.</p>
    </div>

    @if (count($destinations))
        <div class="mt-12 grid auto-rows-[200px] gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:auto-rows-[240px]">
            @foreach ($destinations as $i => $d)
            @php
                $layout = match ($i) {
                    0 => 'lg:col-span-2 lg:row-span-2',
                    3 => 'lg:col-span-2',
                    default => '',
                };
            @endphp
            <a href="{{ Route::has('user.packages.index') ? route('user.packages.index', ['q' => $d['name']]) : '#paket' }}"
               class="photo group rounded-3xl {{ $layout }}">
                <img src="{{ $d['img_url'] ?? asset('images/placeholder.svg') }}" alt="Pemandangan {{ $d['name'] }}" loading="lazy"
                     class="transition-transform duration-500 group-hover:scale-105"
                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d2b3a]/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-5 text-white">
                    <p class="font-display text-2xl font-bold">{{ $d['name'] }}</p>
                    <p class="text-sm text-white/80">{{ $d['region'] }}, {{ $d['count'] }} paket</p>
                </div>
            </a>
            @endforeach
        </div>
    @else
        <p class="mt-12 rounded-3xl bg-[var(--foam)] p-8 text-center text-slate-600">
            Belum ada destinasi yang dipublikasikan.
        </p>
    @endif
</section>

{{-- ========== PAKET ========== --}}
<section id="paket" class="bg-[var(--foam)] py-24">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 class="font-display text-4xl font-bold">Paket wisata pilihan</h2>
            <a href="{{ Route::has('user.packages.index') ? route('user.packages.index') : '#' }}"
               class="text-sm font-semibold text-[var(--lagoon)] underline underline-offset-4">Lihat semua paket</a>
        </div>

        @if (count($packages))
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($packages as $p)
                <article class="flex flex-col overflow-hidden rounded-3xl bg-white">
                    <div class="photo h-56">
                        <img src="{{ $p['img_url'] ?? asset('images/placeholder.svg') }}" alt="{{ $p['title'] }}" loading="lazy"
                             onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';">
                        @if ($p['tag'])
                            <span class="absolute left-4 top-4 rounded-full bg-[var(--sun)] px-3 py-1 text-xs font-semibold text-[var(--ink)]">{{ $p['tag'] }}</span>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="font-display text-xl font-bold leading-snug">{{ $p['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $p['days'] }} hari, hotel, transportasi, dan pemandu termasuk.</p>
                        <div class="mt-auto flex items-end justify-between pt-6">
                            <div>
                                <p class="text-xs text-slate-500">Mulai dari</p>
                                <p class="font-display text-2xl font-bold">Rp {{ number_format($p['price'], 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-500">per orang</p>
                            </div>
                            <a href="{{ $p['url'] ?? (Route::has('register') && ! auth()->check() ? route('register') : '#') }}"
                               class="rounded-full bg-[var(--ink)] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[var(--lagoon)]">Pesan</a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        @else
            <p class="mt-12 rounded-3xl bg-white p-8 text-center text-slate-600">
                Belum ada paket wisata yang dipublikasikan.
            </p>
        @endif
    </div>
</section>

{{-- ========== CARA PESAN (urutan nyata, jadi bernomor) ========== --}}
<section id="cara-pesan" class="mx-auto max-w-7xl px-5 py-24 sm:px-8">
    <h2 class="font-display max-w-xl text-4xl font-bold leading-tight">Empat langkah dari pilih paket sampai berangkat</h2>

    <ol class="mt-14 grid gap-10 md:grid-cols-4">
        @foreach ([
            ['Pilih paket', 'Bandingkan harga, durasi, dan rencana perjalanan. Cek sisa kuota sebelum memesan.'],
            ['Isi data peserta', 'Masukkan nama dan nomor identitas tiap peserta, lalu pakai kode promo bila ada.'],
            ['Bayar', 'Transfer manual atau pembayaran online. Status pesanan berubah otomatis setelah terbayar.'],
            ['Terima konfirmasi', 'Agen memverifikasi pesanan dan mengirim e-voucher serta invoice ke email Anda.'],
        ] as $n => [$title, $desc])
            <li>
                <span class="font-display flex h-12 w-12 items-center justify-center rounded-full bg-[var(--lagoon)] text-lg font-bold text-white">{{ $n + 1 }}</span>
                <h3 class="font-display mt-5 text-xl font-bold">{{ $title }}</h3>
                <p class="mt-2 text-slate-600">{{ $desc }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ========== KEUNGGULAN ========== --}}
<section class="bg-[var(--ink)] py-24 text-white">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 sm:px-8 lg:grid-cols-[1fr_1.4fr]">
        <h2 class="font-display text-4xl font-bold leading-tight">Yang Anda dapat saat memesan lewat kami</h2>
        <dl class="grid gap-x-10 gap-y-8 sm:grid-cols-2">
            <div>
                <dt class="font-display text-lg font-bold text-[var(--sun)]">Harga tertera lengkap</dt>
                <dd class="mt-1 text-white/75">Tidak ada biaya tersembunyi. Apa yang termasuk dan tidak termasuk ditulis di setiap paket.</dd>
            </div>
            <div>
                <dt class="font-display text-lg font-bold text-[var(--sun)]">Rencana Perjalanan hari per hari</dt>
                <dd class="mt-1 text-white/75">Jadwal, lokasi, dan waktu bebas bisa dibaca sebelum Anda membayar.</dd>
            </div>
            <div>
                <dt class="font-display text-lg font-bold text-[var(--sun)]">Mitra hotel dan transport terverifikasi</dt>
                <dd class="mt-1 text-white/75">Setiap vendor kami seleksi dan kami tinjau secara berkala.</dd>
            </div>
            <div>
                <dt class="font-display text-lg font-bold text-[var(--sun)]">Agen yang bisa dihubungi</dt>
                <dd class="mt-1 text-white/75">Ada perubahan jadwal atau pertanyaan? Tim agen membalas lewat WhatsApp dan email.</dd>
            </div>
        </dl>
    </div>
</section>

{{-- ========== TESTIMONI ========== --}}
<section id="testimoni" class="mx-auto max-w-7xl px-5 py-24 sm:px-8">
    <h2 class="font-display text-4xl font-bold">Cerita dari pelanggan</h2>

    <div class="mt-12 grid gap-6 md:grid-cols-3">
        @foreach ([
            ['Rina A.',  'Bali Timur 4D3N',  'Jadwalnya rapi dan pemandunya sabar. Orang tua saya ikut dan tidak kelelahan.'],
            ['Dimas P.', 'Labuan Bajo 3D2N', 'Pesan lewat web, bayar, dan voucher masuk email di hari yang sama.'],
            ['Salsa M.', 'Kyoto & Osaka',     'Tiap hari ada rencana cadangan kalau hujan. Itu sangat membantu.'],
        ] as [$name, $trip, $quote])
            <figure class="rounded-3xl border border-slate-200 p-7">
                <div class="flex gap-0.5 text-[var(--sun)]" aria-label="Rating 5 dari 5">
                    @for ($s = 0; $s < 5; $s++)
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.8 4.8 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z"/></svg>
                    @endfor
                </div>
                <blockquote class="mt-4 text-slate-700">{{ $quote }}</blockquote>
                <figcaption class="mt-5 text-sm"><span class="font-semibold">{{ $name }}</span>, <span class="text-slate-500">{{ $trip }}</span></figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- ========== CTA ========== --}}
<section class="mx-auto max-w-7xl px-5 pb-24 sm:px-8">
    <div class="rounded-[2rem] bg-[var(--lagoon)] px-8 py-14 text-center text-white sm:px-16">
        <h2 class="font-display mx-auto max-w-2xl text-4xl font-bold leading-tight">Tentukan tanggal, kami siapkan sisanya.</h2>
        <p class="mx-auto mt-4 max-w-lg text-white/85">Buat akun untuk menyimpan paket favorit, memantau status pesanan, dan mengunduh invoice.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            @if ($dashboard)
                <a href="{{ $dashboard }}" class="rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-[var(--ink)]">Buka dashboard</a>
            @else
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-full bg-[var(--sun)] px-7 py-3.5 text-sm font-semibold text-[var(--ink)] hover:brightness-95">Daftar gratis</a>
                @endif
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="rounded-full border border-white/70 px-7 py-3.5 text-sm font-semibold hover:bg-white/10">Saya sudah punya akun</a>
                @endif
            @endif
        </div>
    </div>
</section>
</main>

{{-- ========== FOOTER ========== --}}
<footer class="border-t border-slate-200">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:px-8 md:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <p class="font-display text-xl font-extrabold">{{ config('app.name', 'Nusa Jelajah') }}</p>
            <p class="mt-3 max-w-xs text-sm text-slate-600">Agen perjalanan untuk paket wisata domestik dan internasional.</p>
        </div>
        <div>
            <p class="font-display font-bold">Jelajahi</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li><a class="hover:text-[var(--lagoon)]" href="#destinasi">Destinasi</a></li>
                <li><a class="hover:text-[var(--lagoon)]" href="#paket">Paket wisata</a></li>
                <li><a class="hover:text-[var(--lagoon)]" href="#cara-pesan">Cara pesan</a></li>
            </ul>
        </div>
        <div>
            <p class="font-display font-bold">Hubungi kami</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li>info@contoh-travel.id</li>
                <li>+62 812-0000-0000</li>
                <li>Senin sampai Sabtu, 09.00-17.00 WIB</li>
            </ul>
        </div>
    </div>
    <p class="border-t border-slate-200 py-5 text-center text-xs text-slate-500">&copy; {{ date('Y') }} {{ config('app.name', 'Nusa Jelajah') }}. Seluruh hak dilindungi.</p>
</footer>

</body>
</html>
