<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Terra Travel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col">

        {{-- Navbar --}}
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-sky-600">
                        {{ config('app.name', 'Terra Travel') }}
                    </a>

                    {{-- Desktop menu --}}
                    <div class="hidden sm:flex items-center gap-6 text-sm font-medium">
                        <a href="{{ url('/') }}"
                           class="{{ request()->is('/') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">Beranda</a>

                        @if (Route::has('user.packages.index'))
                            <a href="{{ route('user.packages.index') }}"
                               class="{{ request()->routeIs('user.packages.*') ? 'text-sky-600' : 'text-gray-600 hover:text-sky-600' }}">Paket Wisata</a>
                        @endif

                        @auth
                            @if (Route::has('dashboard'))
                                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-sky-600">Dashboard</a>
                            @endif
                            @if (Route::has('profile.edit'))
                                <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-sky-600">{{ Auth::user()->name }}</a>
                            @endif
                            @if (Route::has('logout'))
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-red-600">Keluar</button>
                                </form>
                            @endif
                        @else
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-sky-600">Masuk</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">Daftar</a>
                            @endif
                        @endauth
                    </div>

                    {{-- Hamburger --}}
                    <button @click="open = !open" class="sm:hidden p-2 text-gray-600" aria-label="Menu"
                            aria-controls="mobile-navigation" :aria-expanded="open.toString()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div id="mobile-navigation" x-show="open" x-cloak class="sm:hidden border-t border-gray-200 bg-white px-4 py-3 text-sm">
                <div class="space-y-1">
                <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 hover:text-sky-600">Beranda</a>
                @if (Route::has('user.packages.index'))
                    <a href="{{ route('user.packages.index') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 hover:text-sky-600">Paket Wisata</a>
                @endif
                @auth
                    @if (Route::has('dashboard'))
                        <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 hover:text-sky-600">Dashboard</a>
                    @endif
                    @if (Route::has('profile.edit'))
                        <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 hover:text-sky-600">{{ Auth::user()->name }}</a>
                    @endif
                    @if (Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 pt-2">
                            @csrf
                            <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left font-medium text-red-600 hover:bg-red-50">
                                Keluar
                            </button>
                        </form>
                    @endif
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 hover:text-sky-600">Masuk</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 hover:text-sky-600">Daftar</a>
                    @endif
                @endauth
                </div>
            </div>
        </nav>

        {{-- Header (Breeze slot atau @section) --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{{ $header }}</div>
            </header>
        @endisset
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">@yield('header')</div>
            </header>
        @endif

        {{-- Flash message --}}
        @if (session('success') || session('error'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-md px-4 py-3 text-sm {{ session('success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ session('success') ?? session('error') }}
                </div>
            </div>
        @endif

        {{-- Konten: mendukung <x-app-layout> dan @extends --}}
        <main class="flex-1">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 py-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'Travel Agent') }}. All rights reserved.
        </footer>
    </div>
    @stack('scripts')
</body>
</html>