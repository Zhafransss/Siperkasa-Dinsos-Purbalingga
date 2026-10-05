<x-admin.layout title="Verifikasi Peminjaman">
    <x-admin.page-header title="Verifikasi Peminjaman" subtitle="Tinjau pengajuan peminjaman kendaraan dinas dari pegawai." />

    {{-- Tab --}}
    <div class="mb-stack-md flex gap-1 border-b border-outline-variant" role="tablist">
        <a href="{{ route('admin.bookings.index') }}" role="tab" aria-selected="{{ $tab === 'baru' ? 'true' : 'false' }}"
           @class(['flex items-center gap-2 border-b-2 px-5 py-3 text-label-md transition-colors', 'border-navy text-navy' => $tab === 'baru', 'border-transparent text-on-surface-variant hover:text-navy' => $tab !== 'baru'])>
            Pengajuan Baru
            @if ($pendingCount > 0)
                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-label-sm text-amber-700">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.bookings.index', ['tab' => 'riwayat']) }}" role="tab" aria-selected="{{ $tab === 'riwayat' ? 'true' : 'false' }}"
           @class(['border-b-2 px-5 py-3 text-label-md transition-colors', 'border-navy text-navy' => $tab === 'riwayat', 'border-transparent text-on-surface-variant hover:text-navy' => $tab !== 'riwayat'])>
            Riwayat
        </a>
    </div>

    <form method="GET" action="{{ route('admin.bookings.index') }}" role="search" data-autosubmit class="mb-stack-md">
        @if ($tab === 'riwayat') <input type="hidden" name="tab" value="riwayat"> @endif
        <label class="flex items-center gap-3 rounded-xl border border-outline-variant bg-white px-4 py-3">
            <span class="material-symbols-outlined text-outline">search</span>
            <input type="text" name="q" value="{{ $term }}" maxlength="100" placeholder="Cari pengajuan..." aria-label="Cari pengajuan"
                   class="w-full border-0 bg-transparent p-0 text-body-sm focus:ring-0">
        </label>
    </form>

    <div class="space-y-4">
        @forelse ($bookings as $booking)
            @php $status = $booking->effectiveStatus(); @endphp
            <article class="rounded-xl border border-outline-variant bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <div class="flex items-center gap-4 lg:w-[30%]">
                        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-secondary-fixed text-label-md text-navy">{{ $booking->employee->initials() }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-label-md text-on-background">{{ $booking->employee->name }}</p>
                            <p class="text-label-sm text-on-surface-variant">{{ $booking->employee->bidangShort() }} · {{ $booking->displayId() }}</p>
                        </div>
                    </div>

                    <div class="grid min-w-0 flex-1 grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Kendaraan</p>
                            <p class="truncate text-body-sm font-semibold text-on-background">{{ $booking->vehicle->name }}</p>
                            <p class="text-label-sm text-on-surface-variant">{{ $booking->vehicle->plate }}</p>
                        </div>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Tanggal</p>
                            <p class="text-body-sm font-semibold text-on-background">{{ $booking->shortScheduleLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Tujuan</p>
                            <p class="truncate text-body-sm font-semibold text-on-background" title="{{ $booking->destination }}">{{ $booking->destination }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 lg:justify-end">
                        @if ($tab === 'riwayat')
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-label-sm {{ $status->badgeClasses() }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $status->icon() }}</span>{{ $status->label() }}
                            </span>
                        @endif
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="rounded-lg bg-navy px-5 py-2.5 text-label-md text-white transition-all hover:brightness-110">
                            {{ $tab === 'baru' ? 'Tinjau' : 'Detail' }}
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest py-stack-lg text-center text-body-sm text-on-surface-variant">
                @if ($term !== '') Tidak ada pengajuan yang cocok dengan pencarian.
                @elseif ($tab === 'baru') Tidak ada pengajuan yang menunggu verifikasi.
                @else Belum ada riwayat pengajuan. @endif
            </p>
        @endforelse
    </div>

    {{ $bookings->links('vendor.pagination.admin', ['unit' => 'pengajuan']) }}
</x-admin.layout>
