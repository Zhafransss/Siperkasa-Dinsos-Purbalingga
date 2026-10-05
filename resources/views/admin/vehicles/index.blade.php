<x-admin.layout title="Manajemen Kendaraan">
    <x-admin.page-header title="Manajemen Kendaraan" subtitle="Pantau dan kelola seluruh aset operasional Dinas Kesehatan.">
        <x-slot:actions>
            <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-navy px-6 py-3 text-label-md text-white shadow-[0_4px_6px_-4px_rgba(0,42,93,.2),0_10px_15px_-3px_rgba(0,42,93,.2)] transition-all hover:brightness-110">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Kendaraan
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Pencarian & filter --}}
    <form method="GET" action="{{ route('admin.vehicles.index') }}" role="search" data-autosubmit class="mb-stack-lg grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto_auto]">
        <label class="flex items-center gap-3 rounded-xl border border-outline-variant bg-white px-4 py-3">
            <span class="material-symbols-outlined text-outline">search</span>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Cari nama, merk, atau plat nomor..." aria-label="Cari kendaraan"
                   class="w-full border-0 bg-transparent p-0 text-body-sm focus:ring-0">
        </label>
        <select name="status" aria-label="Filter status" class="rounded-xl border border-outline-variant bg-white px-4 py-3 text-label-md text-on-surface-variant focus:border-navy focus:ring-2 focus:ring-navy/20">
            <option value="">Status: Semua</option>
            @foreach (\App\Enums\VehicleStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>Status: {{ $status->adminLabel() }}</option>
            @endforeach
        </select>
        <select name="category" aria-label="Filter kategori" class="rounded-xl border border-outline-variant bg-white px-4 py-3 text-label-md text-on-surface-variant focus:border-navy focus:ring-2 focus:ring-navy/20">
            <option value="">Kategori: Semua</option>
            @foreach (\App\Enums\VehicleCategory::cases() as $category)
                <option value="{{ $category->value }}" @selected(($filters['category'] ?? null) === $category->value)>Kategori: {{ $category->label() }}</option>
            @endforeach
        </select>
        <noscript><button type="submit" class="rounded-lg bg-navy px-4 py-2 text-white">Terapkan</button></noscript>
    </form>

    {{-- Kartu kendaraan --}}
    <div class="grid grid-cols-1 gap-gutter sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($vehicles as $vehicle)
            <article class="flex flex-col overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm">
                <div class="relative h-48 overflow-hidden bg-surface-container-low">
                    @if ($vehicle->imageUrl())
                        <img class="h-full w-full object-cover" loading="lazy" alt="{{ $vehicle->name }}" src="{{ $vehicle->imageUrl() }}">
                    @else
                        <div class="flex h-full items-center justify-center text-outline"><span class="material-symbols-outlined text-[64px]">directions_car</span></div>
                    @endif
                    <span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-label-sm shadow-sm {{ $vehicle->status->adminPillClasses() }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $vehicle->status->adminLabel() }}
                    </span>
                    <span class="absolute bottom-3 right-3 rounded-lg bg-black/50 px-3 py-1 text-label-sm text-white">{{ $vehicle->category->pillLabel() }}</span>
                </div>

                <div class="flex flex-grow flex-col p-5">
                    <h2 class="text-headline-sm text-on-background">{{ $vehicle->plate }}</h2>
                    <p class="text-body-sm text-on-surface-variant">{{ $vehicle->modelLine() }}</p>

                    <div class="mt-4 flex items-center gap-2 border-t border-outline-variant pt-4 text-label-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px]">build</span>
                        Terakhir servis:
                        <span class="ml-auto text-body-sm font-semibold text-on-background">{{ $vehicle->last_serviced_on?->translatedFormat('j M Y') ?? '-' }}</span>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="flex-1 rounded-lg bg-surface-container-high py-3 text-center text-label-md text-navy transition-colors hover:bg-outline-variant/60">Detail</a>
                        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                              data-confirm-title="Hapus Kendaraan" data-confirm-ok="Ya, Hapus"
                              data-confirm-message="Hapus {{ $vehicle->name }} ({{ $vehicle->plate }})? Tindakan ini tidak dapat dibatalkan.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Hapus {{ $vehicle->name }}" class="flex h-full items-center rounded-lg border border-outline-variant px-3 text-on-surface-variant transition-colors hover:border-error hover:text-error">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest py-stack-lg text-center text-body-sm text-on-surface-variant">
                Tidak ada kendaraan yang cocok. <a href="{{ route('admin.vehicles.index') }}" class="text-navy hover:underline">Hapus filter</a>
            </p>
        @endforelse

        {{-- Tile "Tambah Slot Baru": shown after the last card --}}
        @unless ($vehicles->hasMorePages())
            <a href="{{ route('admin.vehicles.create') }}" class="flex min-h-[16rem] flex-col items-center justify-center rounded-xl border-2 border-dashed border-outline-variant bg-surface-container p-8 text-center transition-colors hover:bg-surface-container-high">
                <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-surface-variant text-outline"><span class="material-symbols-outlined text-[28px]">add</span></span>
                <span class="text-label-md text-on-surface">Tambah Slot Baru</span>
                <span class="mt-2 max-w-[14rem] text-body-sm text-outline">Input data kendaraan baru ke dalam sistem armada.</span>
            </a>
        @endunless
    </div>

    {{-- Legenda jumlah per status + pager --}}
    <div class="mt-stack-lg flex flex-wrap items-center gap-x-6 gap-y-2 rounded-xl border border-outline-variant bg-surface-container-low px-5 py-4 text-label-sm text-on-surface-variant">
        @foreach (\App\Enums\VehicleStatus::cases() as $status)
            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full {{ $status->dotClasses() }}"></span>{{ $status->adminLabel() }}: {{ $counts[$status->value] ?? 0 }}</span>
        @endforeach
    </div>

    {{ $vehicles->links('vendor.pagination.admin', ['unit' => 'kendaraan']) }}
</x-admin.layout>
