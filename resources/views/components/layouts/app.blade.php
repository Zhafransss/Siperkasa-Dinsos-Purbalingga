@props(['title' => null])

@php
    $nav = [
        ['label' => 'Beranda', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Katalog Mobil', 'href' => route('katalog'), 'active' => request()->routeIs('katalog')],
        ['label' => 'Status Peminjaman', 'href' => route('status.index'), 'active' => request()->routeIs('status.*')],
        ['label' => 'Kontak', 'href' => '#kontak', 'active' => false],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }} · Peminjaman Kendaraan Dinas Dinkes PPKB Purbalingga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background antialiased overflow-x-hidden">

<header class="fixed top-0 left-0 z-50 w-full border-b border-outline-variant bg-surface">
    <div class="mx-auto flex h-20 max-w-container-max items-center justify-between px-margin-mobile py-4 md:px-margin-desktop">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img class="h-10 w-10 object-contain" alt="Logo Kabupaten Purbalingga" src="{{ asset('images/logo-dinkes.png') }}">
            <span>
                <span class="block text-headline-sm font-bold leading-6 text-primary">{{ config('app.name') }}</span>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-outline">Dinkes PPKB Purbalingga</span>
            </span>
        </a>

        <nav class="hidden items-center gap-12 md:flex" aria-label="Navigasi utama">
            @foreach ($nav as $item)
                <a href="{{ $item['href'] }}"
                   @class([
                       'pb-1 text-label-md transition-colors',
                       'border-b-2 border-primary text-primary' => $item['active'],
                       'text-on-surface-variant hover:text-primary' => ! $item['active'],
                   ])>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        {{-- The icon class sets its own display, so the breakpoint utility must live on the button wrapper. --}}
        <button type="button" class="md:hidden" data-mobile-nav-toggle aria-label="Buka menu" aria-expanded="false"><span class="material-symbols-outlined">menu</span></button>
    </div>

    <nav id="mobile-nav" class="hidden flex-col gap-3 border-t border-outline-variant bg-surface p-4 md:hidden" aria-label="Navigasi seluler">
        @foreach ($nav as $item)
            <a href="{{ $item['href'] }}" @class(['text-label-md', 'text-primary' => $item['active']])>{{ $item['label'] }}</a>
        @endforeach
    </nav>
</header>

<main class="pt-20">
    {{ $slot }}
</main>

<footer id="kontak" class="mt-stack-md w-full scroll-mt-20 bg-[#0b0f1e] px-margin-mobile py-8 text-white md:px-margin-desktop">
    <div class="mx-auto grid max-w-container-max grid-cols-1 gap-8 md:grid-cols-3">
        <div>
            <h3 class="mb-3 text-headline-sm font-bold text-white">{{ config('app.name') }} · Dinkes PPKB Purbalingga</h3>
            <p class="mb-1 text-label-md font-bold text-slate-200">Tentang Kami</p>
            <p class="max-w-xs text-body-sm text-slate-400">Sistem Manajemen Transportasi Terpadu untuk menunjang mobilitas layanan kesehatan masyarakat di Kabupaten Purbalingga.</p>
        </div>
        <div>
            <h4 class="mb-3 text-headline-sm font-bold text-white">Kontak Kami</h4>
            <div class="mb-2 flex items-start gap-2.5 text-slate-300">
                <span class="material-symbols-outlined mt-0.5 text-[20px] text-emerald-400">location_on</span>
                <p class="text-body-sm">Jl. Letjen S Parman No.21, Bancar, Kec. Purbalingga, Kabupaten Purbalingga, Jawa Tengah 53316</p>
            </div>
            <div class="mb-2 flex items-center gap-2.5 text-slate-300">
                <span class="material-symbols-outlined text-[20px] text-emerald-400">mail</span>
                <a class="text-body-sm transition-colors hover:text-white" href="mailto:dinkes@purbalinggakab.go.id">dinkes@purbalinggakab.go.id</a>
            </div>
            <div class="mb-4 flex items-center gap-2.5 text-slate-300">
                <span class="material-symbols-outlined text-[20px] text-emerald-400">call</span>
                <p class="text-body-sm">(0281) 891034</p>
            </div>
            <p class="mb-2 text-label-md font-bold text-slate-200">Ikuti Kami</p>
            <div class="flex gap-4">
                <a href="#" class="text-slate-300 transition-colors hover:text-white" aria-label="Facebook">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="4"/><path d="M14 8h-1.5A2.5 2.5 0 0 0 10 10.5V12H8v3h2v6h3v-6h2.2l.3-3H13v-1.2c0-.4.3-.8.8-.8H15V8z" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="#" class="text-slate-300 transition-colors hover:text-white" aria-label="X">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>
                </a>
                <a href="#" class="text-slate-300 transition-colors hover:text-white" aria-label="Instagram">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="3.7"/><circle cx="17.2" cy="6.8" r="1"/></svg>
                </a>
                <a href="#" class="text-slate-300 transition-colors hover:text-white" aria-label="YouTube">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2.5" y="5.5" width="19" height="13" rx="3.5"/><path d="M10.5 9.5l5 2.5-5 2.5v-5z" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="#" class="text-slate-300 transition-colors hover:text-white" aria-label="TikTok">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19.6 6.7a4.9 4.9 0 0 1-3.8-4.2V2h-3.4v13.3a2.9 2.9 0 1 1-2.9-2.9c.3 0 .6 0 .8.1V9a6.3 6.3 0 1 0 5.5 6.3V8.9a8.2 8.2 0 0 0 4.800 1.500V7a4.900 4.900 0 0 1-1-.3z"/></svg>
                </a>
            </div>
        </div>
        <div>
            <h4 class="mb-3 text-headline-sm font-bold text-white">Jam Layanan</h4>
            <div class="flex items-start gap-2.5 text-slate-300">
                <span class="material-symbols-outlined mt-0.5 text-[20px] text-emerald-400">schedule</span>
                <dl class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-1.5 text-body-sm">
                    <dt class="text-slate-400">Senin – Kamis</dt>
                    <dd>07.30 – 16.00 WIB</dd>
                    <dt class="text-slate-400">Jumat</dt>
                    <dd>07.30 – 16.30 WIB</dd>
                    <dt class="text-slate-400">Sabtu – Minggu</dt>
                    <dd>Tutup</dd>
                    <dt class="text-slate-400">Hari libur nasional</dt>
                    <dd>Tutup</dd>
                </dl>
            </div>
            <p class="mt-3 max-w-xs text-label-sm text-slate-500">Pengambilan kunci dan pengembalian kendaraan dilayani pada jam kerja di atas.</p>
        </div>
    </div>
    <div class="mx-auto mt-6 max-w-container-max border-t border-white/10 pt-4">
        <p class="text-label-sm text-slate-500">© {{ now()->year }} Dinas Kesehatan PPKB Kabupaten Purbalingga. Hak Cipta Dilindungi.</p>
    </div>
</footer>

@include('partials.feedback')

</body>
</html>
