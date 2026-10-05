@props(['title' => null, 'active' => 'dashboard'])

@php
    $nav = [
        ['key' => 'dashboard', 'label' => 'Beranda', 'icon' => 'dashboard', 'href' => route('admin.dashboard'), 'on' => request()->routeIs('admin.dashboard')],
        ['key' => 'vehicles', 'label' => 'Manajemen Kendaraan', 'icon' => 'directions_car', 'href' => route('admin.vehicles.index'), 'on' => request()->routeIs('admin.vehicles.*')],
        ['key' => 'bookings', 'label' => 'Verifikasi Peminjaman', 'icon' => 'fact_check', 'href' => route('admin.bookings.index'), 'on' => request()->routeIs('admin.bookings.*')],
        ['key' => 'employees', 'label' => 'Manajemen Pegawai', 'icon' => 'badge', 'href' => route('admin.employees.index'), 'on' => request()->routeIs('admin.employees.*')],
        ['key' => 'admins', 'label' => 'Manajemen Admin', 'icon' => 'admin_panel_settings', 'href' => route('admin.admins.index'), 'on' => request()->routeIs('admin.admins.*')],
    ];
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ? $title.' — ' : '' }}Admin · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background antialiased">

{{-- Sidebar: fixed on desktop, slides in as a drawer on small screens --}}
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col justify-between border-r border-outline-variant bg-surface py-8 transition-transform duration-200 lg:translate-x-0" aria-label="Menu admin">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="mb-10 flex items-center gap-3 px-6">
            <img class="h-10 w-10 object-contain" alt="Logo Kabupaten Purbalingga" src="{{ asset('images/logo-dinkes.png') }}">
            <span>
                <span class="block text-headline-sm font-bold leading-6 text-navy">{{ config('app.name') }}</span>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-outline">Dinkes PPKB Purbalingga</span>
            </span>
        </a>

        <nav class="flex flex-col gap-1 px-4">
            @foreach ($nav as $item)
                <a href="{{ $item['href'] }}"
                   @if ($item['on']) aria-current="page" @endif
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-3 text-label-md transition-colors',
                       'border-r-4 border-navy bg-secondary-fixed text-navy' => $item['on'],
                       'text-sidebar hover:bg-surface-container-low' => ! $item['on'],
                   ])>
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <form method="POST" action="{{ route('admin.logout') }}" class="px-4">
        @csrf
        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-label-md text-error transition-colors hover:bg-error-container/40">
            <span class="material-symbols-outlined text-[20px]">logout</span>Keluar
        </button>
    </form>
</aside>
<div id="admin-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

{{-- Top bar --}}
<header class="fixed left-0 right-0 top-0 z-30 flex h-16 items-center justify-between border-b border-outline-variant bg-white/80 px-4 backdrop-blur-md md:px-10 lg:left-64">
    <div class="flex items-center gap-3">
        <button type="button" data-admin-menu-toggle class="rounded-lg p-1.5 text-on-surface-variant hover:bg-surface-container-low lg:hidden" aria-label="Buka menu" aria-controls="admin-sidebar" aria-expanded="false">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <span class="text-headline-sm font-black text-navy">{{ config('app.name') }}</span>
    </div>

    <div class="flex items-center gap-3">
        <div class="hidden text-right sm:block">
            <p class="text-label-md leading-5 text-navy">{{ $user->name }}</p>
            <p class="text-[10px] text-outline">Administrator{{ $user->bidang ? ' · '.$user->bidang : '' }}</p>
        </div>
        <span class="flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant bg-secondary-fixed text-label-md font-bold text-navy" aria-hidden="true">{{ $user->initials() }}</span>
    </div>
</header>

<main class="pt-16 lg:pl-64">
    <div class="mx-auto max-w-[1100px] px-4 pb-12 pt-8 md:px-10">
        {{ $slot }}
    </div>
</main>

@include('partials.feedback')
</body>
</html>
