@props(['vehicle', 'compact' => false])

{{--
    Vehicle card (Figma "Katalog Mobil Dinas"): photo with status badge and capacity label, name, short
    description, action button. `compact` is the Beranda variant: subtitle "CATEGORY • PLAT" and a booked
    vehicle is labelled "Booking"; the default is the Katalog variant (descriptive subtitle).
--}}
@php
    $status = $vehicle->status;
    $badgeLabel = $compact && $status === \App\Enums\VehicleStatus::Dipakai ? 'Booking' : $status->label();
    $subtitle = $compact ? strtoupper($vehicle->category->label()).' • '.$vehicle->plate : ($vehicle->description ?: $vehicle->modelLine());
@endphp

<div class="flex flex-col overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm">
    <div class="relative h-48 overflow-hidden bg-surface-container-low">
        @if ($vehicle->imageUrl())
            <img class="h-full w-full object-cover" loading="lazy" alt="{{ $vehicle->name }}" src="{{ $vehicle->imageUrl() }}">
        @else
            <div class="flex h-full items-center justify-center text-outline">
                <span class="material-symbols-outlined text-[64px]">directions_car</span>
            </div>
        @endif
        <span class="absolute left-3 top-3 whitespace-nowrap rounded-sm px-2 py-1 text-[10px] font-bold uppercase leading-none {{ $status->badgeClasses() }}">{{ $badgeLabel }}</span>
        @if ($vehicle->capacityLabel())
            <span class="absolute bottom-3 left-3 flex items-center gap-1.5 rounded-sm bg-white/90 px-2 py-1 text-label-sm text-on-surface shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant">groups</span>{{ $vehicle->capacityLabel() }}
            </span>
        @endif
    </div>

    <div class="flex flex-grow flex-col p-6">
        <h4 class="text-headline-sm">{{ $vehicle->name }}</h4>
        <p class="mt-1 text-body-sm text-on-surface-variant">{{ $subtitle }}</p>

        <div class="mt-auto pt-6">
            @if ($vehicle->isBookable())
                <a href="{{ route('booking.create', ['vehicle' => $vehicle->id]) }}"
                   class="block w-full rounded-lg bg-primary py-2.5 text-center text-label-md text-white transition-colors hover:bg-primary-container">Pilih Kendaraan</a>
            @else
                <button type="button" disabled
                        class="w-full cursor-not-allowed rounded-lg bg-outline-variant/60 py-2.5 text-label-md text-on-surface-variant/50">{{ $compact && $status === \App\Enums\VehicleStatus::Dipakai ? 'Sedang Digunakan' : $status->unavailableButtonLabel() }}</button>
            @endif
        </div>
    </div>
</div>
