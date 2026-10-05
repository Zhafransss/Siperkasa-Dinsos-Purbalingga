@props(['title', 'icon' => 'info', 'tone' => 'navy'])

{{-- Form card with an icon tile and a heading (Figma "Informasi Dasar" etc.). --}}
@php
    $tile = ['navy' => 'bg-primary text-primary-fixed-dim', 'soft' => 'bg-secondary-fixed text-navy', 'grey' => 'bg-surface-container-high text-on-surface-variant'][$tone] ?? 'bg-primary text-primary-fixed-dim';
@endphp

<section class="rounded-xl border border-outline-variant bg-white p-5 shadow-sm sm:p-8">
    <div class="mb-6 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $tile }}"><span class="material-symbols-outlined text-[18px]">{{ $icon }}</span></span>
        <h2 class="text-headline-sm text-on-background">{{ $title }}</h2>
    </div>
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">
        {{ $slot }}
    </div>
</section>
