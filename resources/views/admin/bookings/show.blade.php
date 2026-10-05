@php
    $status = $booking->effectiveStatus();
    $pending = $booking->status === \App\Enums\BookingStatus::Menunggu;
@endphp

<x-admin.layout title="Detail Pengajuan">
    <nav class="mb-3 flex items-center gap-2 text-label-sm text-on-surface-variant" aria-label="Breadcrumb">
        <a href="{{ route('admin.bookings.index') }}" class="hover:text-navy hover:underline">Verifikasi Peminjaman</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-bold text-navy">{{ $booking->displayId() }}</span>
    </nav>

    <div class="mb-stack-lg flex flex-wrap items-center gap-3">
        <h1 class="text-headline-md text-navy">Detail Pengajuan</h1>
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-label-sm {{ $status->badgeClasses() }}">
            <span class="material-symbols-outlined text-[14px]">{{ $status->icon() }}</span>{{ $status->label() }}
        </span>
    </div>

    @if ($conflict)
        <div class="mb-stack-md flex items-start gap-3 rounded-xl border border-error bg-error-container p-4 text-error" role="alert">
            <span class="material-symbols-outlined mt-0.5">warning</span>
            <p class="text-body-sm font-medium">{{ $conflict }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-gutter lg:grid-cols-3">
        <div class="space-y-gutter lg:col-span-2">
            <x-admin.form-section title="Data Pemohon" icon="person">
                <x-admin.detail label="Nama Pegawai" :value="$booking->employee->name" />
                <x-admin.detail label="NIP" :value="$booking->employee->nip" />
                <x-admin.detail label="Bidang / Unit Kerja" :value="$booking->employee->bidang" />
                <x-admin.detail label="Jabatan" :value="$booking->employee->jabatan ?: '-'" />
            </x-admin.form-section>

            <x-admin.form-section title="Detail Perjalanan" icon="route" tone="soft">
                <x-admin.detail label="Tanggal Keberangkatan" :value="$booking->departs_on->translatedFormat('l, j F Y')" />
                <x-admin.detail label="Tanggal Kembali" :value="$booking->returns_on->translatedFormat('l, j F Y')" />
                <x-admin.detail label="Tujuan" :value="$booking->destination" />
                <x-admin.detail label="Keperluan" :value="$booking->purpose" />
            </x-admin.form-section>

            @if ($booking->admin_note)
                <section class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                    <p class="text-label-md text-on-surface-variant">{{ $booking->status === \App\Enums\BookingStatus::Ditolak ? 'Alasan Penolakan' : 'Catatan Admin' }}</p>
                    <p class="mt-1 text-body-md text-on-background">{{ $booking->admin_note }}</p>
                </section>
            @endif
        </div>

        <aside class="space-y-gutter">
            <section class="overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm">
                @if ($booking->vehicle->imageUrl())
                    <img src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}" class="h-44 w-full object-cover">
                @endif
                <div class="p-5">
                    <p class="text-label-sm text-on-surface-variant">Kendaraan</p>
                    <p class="text-headline-sm text-on-background">{{ $booking->vehicle->name }}</p>
                    <p class="text-body-sm text-on-surface-variant">{{ $booking->vehicle->plate }} · {{ $booking->vehicle->modelLine() }}</p>
                </div>
            </section>

            @if ($pending)
                <section class="space-y-3 rounded-xl border border-outline-variant bg-white p-5 shadow-sm">
                    <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}"
                          data-confirm-title="Setujui Pengajuan" data-confirm-ok="Ya, Setujui" data-confirm-tone="primary"
                          data-confirm-message="Setujui peminjaman {{ $booking->vehicle->name }} oleh {{ $booking->employee->name }} pada {{ $booking->shortScheduleLabel() }}?"
                          data-confirm-input="admin_note" data-confirm-input-label="Catatan (opsional)">
                        @csrf
                        <button type="submit" @disabled($conflict) class="flex w-full items-center justify-center gap-2 rounded-lg bg-navy px-5 py-3 text-label-md text-white transition-all hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span> Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}"
                          data-confirm-title="Tolak Pengajuan" data-confirm-ok="Ya, Tolak"
                          data-confirm-message="Tolak pengajuan {{ $booking->displayId() }}? Alasan akan terlihat oleh pemohon."
                          data-confirm-input="admin_note" data-confirm-input-label="Alasan penolakan" data-confirm-input-required>
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-error px-5 py-3 text-label-md text-error transition-colors hover:bg-error-container">
                            <span class="material-symbols-outlined text-[18px]">cancel</span> Tolak
                        </button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
</x-admin.layout>
