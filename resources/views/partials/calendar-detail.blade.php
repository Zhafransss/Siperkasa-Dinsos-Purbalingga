@foreach ($bookings as $booking)
    <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5">
        <div class="mb-2 flex items-start justify-between gap-2">
            <h4 class="pr-2 text-headline-sm text-on-background">{{ $booking->vehicle->name }}</h4>
            <span class="whitespace-nowrap rounded-sm px-2 py-1 text-[10px] font-bold uppercase {{ $booking->status === \App\Enums\BookingStatus::Disetujui ? 'bg-green-100 text-green-800' : 'bg-[#fff8e1] text-amber-700' }}">{{ $booking->status->label() }}</span>
        </div>
        <p class="mb-3 text-body-sm text-on-surface-variant">PLAT NO: {{ $booking->vehicle->plate }}</p>
        <hr class="mb-3 border-outline-variant">
        <div class="mb-1 flex items-center gap-2 text-body-sm text-on-surface">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">person</span>
            {{ $booking->employee->name }}
            {{-- NIP is masked here because this list is public to every staff member. --}}
            <span class="text-on-surface-variant">NIP: {{ $booking->employee->maskedNip() }}</span>
        </div>
        <p class="mb-2 ml-6 text-body-sm text-on-surface-variant">Bidang: {{ $booking->employee->bidangShort() }}</p>
        <div class="mb-1 flex items-center gap-2 text-body-sm text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">schedule</span>{{ $booking->scheduleLabel() }}</div>
        <div class="mb-2 flex items-center gap-2 text-body-sm text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">location_on</span>{{ $booking->destination }}</div>
        <p class="mb-2 text-body-sm italic text-on-surface-variant">"{{ $booking->purpose }}"</p>
        @if ($booking->admin_note)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-2 text-body-sm text-amber-800"><strong>Catatan Admin:</strong> {{ $booking->admin_note }}</div>
        @endif
    </div>
@endforeach

@foreach ($maintenances as $maintenance)
    <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-5">
        <div class="mb-2 flex items-start justify-between gap-2">
            <h4 class="pr-2 text-headline-sm text-on-background">{{ $maintenance->vehicle->name }}</h4>
            <span class="whitespace-nowrap rounded-sm bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase text-amber-700">Perawatan</span>
        </div>
        <p class="mb-3 text-body-sm text-on-surface-variant">PLAT NO: {{ $maintenance->vehicle->plate }}</p>
        <hr class="mb-3 border-amber-200">
        <div class="mb-1 flex items-center gap-2 text-body-sm text-on-surface-variant">
            <span class="material-symbols-outlined text-[18px]">build</span>
            {{ $maintenance->reason ?: 'Perawatan kendaraan' }}
        </div>
        <div class="flex items-center gap-2 text-body-sm text-on-surface-variant">
            <span class="material-symbols-outlined text-[18px]">event</span>
            {{ $maintenance->starts_on->format('Y-m-d') }} s/d {{ $maintenance->ends_on->format('Y-m-d') }}
        </div>
    </div>
@endforeach

@if ($bookings->isEmpty() && $maintenances->isEmpty())
    <div class="rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest py-stack-lg text-center text-body-sm text-on-surface-variant md:col-span-2">
        Belum ada peminjaman atau perawatan untuk tanggal ini.
    </div>
@endif
