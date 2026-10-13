@php
    $editing = $vehicle->exists;
    $action = $editing ? route('admin.vehicles.update', $vehicle) : route('admin.vehicles.store');
    $yearOptions = collect($years)->mapWithKeys(fn ($y) => [$y => $y])->all();
@endphp

<x-admin.layout :title="$editing ? 'Detail Kendaraan' : 'Tambah Kendaraan'">
    {{-- Breadcrumb --}}
    <nav class="mb-3 flex items-center gap-2 text-label-sm text-on-surface-variant" aria-label="Breadcrumb">
        <a href="{{ route('admin.vehicles.index') }}" class="hover:text-navy hover:underline">Manajemen Kendaraan</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-bold text-navy">{{ $editing ? 'Detail Kendaraan' : 'Tambah Kendaraan' }}</span>
    </nav>

    @if ($editing)
        <div class="mb-stack-lg flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-headline-md text-navy">{{ $vehicle->name }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <span class="rounded-lg bg-navy px-3 py-1 text-label-md text-white">{{ $vehicle->plate }}</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-success bg-success/10 px-3 py-1 text-label-md text-success">
                        <span class="h-1.5 w-1.5 rounded-full {{ $vehicle->status->dotClasses() }}"></span>{{ $vehicle->status->adminLabel() }}
                    </span>
                </div>
            </div>
        </div>
    @else
        <x-admin.page-header title="Tambah Kendaraan Baru" subtitle="Lengkapi formulir di bawah ini untuk mendaftarkan unit kendaraan baru ke dalam sistem operasional." />
    @endif

    @if ($errors->any())
        <div class="mb-stack-md flex items-start gap-3 rounded-xl border border-error bg-error-container p-4 text-error" role="alert">
            <span class="material-symbols-outlined mt-0.5">error</span>
            <p class="text-body-sm font-medium">Data belum bisa disimpan. Periksa kolom yang ditandai merah di bawah.</p>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-stack-lg" novalidate>
        @csrf
        @if ($editing) @method('PUT') @endif

        {{-- Hidden fields for preserved attributes --}}
        <input type="hidden" name="category" value="{{ old('category', $vehicle->category?->value ?? \App\Enums\VehicleCategory::Operasional->value) }}">

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Kartu Kiri: Informasi Dasar --}}
            <section class="flex flex-col justify-between rounded-xl border border-outline-variant bg-white p-5 shadow-sm sm:p-7 lg:col-span-7 xl:col-span-8">
                <div>
                    <div class="mb-6 flex items-center gap-2 text-navy">
                        <span class="material-symbols-outlined text-[22px]">info</span>
                        <h2 class="text-base font-medium">Informasi Dasar</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-x-5 gap-y-5 sm:grid-cols-2">
                        <x-admin.field filled name="name" label="Nama Kendaraan" :value="$vehicle->name" placeholder="Toyota Hiace Premio" maxlength="255" required />
                        <x-admin.field filled name="plate" label="Nomor Polisi" :value="$vehicle->plate" placeholder="R 1234 PA" maxlength="20" required />
                        <x-admin.select filled name="year" label="Tahun Produksi" :options="$yearOptions" :value="$vehicle->year" required />
                        <x-admin.field filled name="color" label="Warna Kendaraan" :value="$vehicle->color" placeholder="Putih Kristal" maxlength="50" required />
                        <x-admin.select filled name="status" label="Status Operasional" :options="$statuses" :value="$vehicle->status" span="sm:col-span-2" required />
                    </div>
                </div>
            </section>

            {{-- Kartu Kanan: Spesifikasi Teknis --}}
            <section class="rounded-xl border border-outline-variant bg-white p-5 shadow-sm sm:p-7 lg:col-span-5 xl:col-span-4">
                <div class="mb-6 flex items-center gap-2 text-navy">
                    <span class="material-symbols-outlined text-[22px]">settings</span>
                    <h2 class="text-base font-medium">Spesifikasi Teknis</h2>
                </div>

                <div class="flex flex-col gap-5">
                    <x-admin.select filled name="fuel_type" label="Jenis Bahan Bakar" :options="$fuelTypes" :value="$vehicle->fuel_type" placeholder="Pilih jenis bahan bakar" required />
                    <x-admin.field filled name="capacity" label="Kapasitas Penumpang" type="number" :value="$vehicle->capacity" min="1" max="60" inputmode="numeric" placeholder="12" />
                    <x-admin.field filled name="odometer_km" label="Odometer Saat Ini (Km)" type="number" :value="$vehicle->odometer_km" min="0" inputmode="numeric" placeholder="12450" required />
                    <x-admin.field filled name="last_serviced_on" label="Terakhir Servis" type="date" :value="$vehicle->last_serviced_on?->toDateString()" :max="today()->toDateString()" />
                </div>
            </section>
        </div>

        {{-- Foto: satu berkas per sisi; JPG/PNG maksimal 5MB --}}
        <section class="rounded-xl border border-outline-variant bg-white p-5 shadow-sm sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">photo_camera</span></span>
                <h2 class="text-headline-sm text-on-background">{{ $editing ? 'Galeri Foto Kendaraan' : 'Unggah Foto Kendaraan' }}</h2>
            </div>

            {{-- Slots are filled in order: every photo already stored, plus one empty "tambah foto" card; choosing a file reveals the next one (max 4). --}}
            @php $emptyShown = false; @endphp
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach (\App\Enums\PhotoSide::cases() as $side)
                    @php
                        $photo = $editing ? $vehicle->photoFor($side) : null;
                        $photoError = $errors->first('photos.'.$side->value);
                        $visible = $photo || $photoError || ! $emptyShown;
                        $emptyShown = $emptyShown || ! $photo;
                    @endphp
                    <div data-photo-slot @class(['relative flex flex-col gap-2', 'hidden' => ! $visible])>
                        <label @class([
                            'relative flex h-44 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed bg-surface-container-lowest transition-colors hover:bg-surface-container-low',
                            'border-outline-variant' => ! $photoError,
                            'border-error' => $photoError,
                        ])>
                            <input type="file" name="photos[{{ $side->value }}]" accept="image/jpeg,image/png" class="sr-only" data-photo-input aria-label="Foto {{ $side->label() }}">
                            <img data-photo-preview alt="Foto {{ $side->label() }}" src="{{ $photo?->url() }}" class="{{ $photo ? '' : 'hidden' }} absolute inset-0 h-full w-full object-cover">
                            <span data-photo-empty class="{{ $photo ? 'hidden' : 'flex' }} flex-col items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[28px]">add_a_photo</span>
                            </span>
                        </label>
                        {{-- X: deletes a stored photo (confirmation dialog) --}}
                        @if ($photo)
                            <button type="submit" form="delete-photo-{{ $side->value }}" aria-label="Hapus {{ strtolower($side->label()) }}" title="Hapus foto"
                                    class="absolute right-2 top-2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-on-surface-variant shadow-md ring-1 ring-outline-variant transition-colors hover:bg-error hover:text-white">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        @endif
                        {{-- X: discards a file that was just chosen and not yet saved --}}
                        <button type="button" data-photo-clear aria-label="Batalkan foto" title="Batalkan foto"
                                class="absolute right-2 top-2 z-10 hidden h-8 w-8 items-center justify-center rounded-full bg-white/90 text-on-surface-variant shadow-md ring-1 ring-outline-variant transition-colors hover:bg-error hover:text-white">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                        @if ($photoError)
                            <p class="text-label-sm text-error">{{ $photoError }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <p class="mt-4 flex items-center gap-2 text-body-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[16px]">info</span>
                Ukuran foto maksimal 5MB per file. Format yang didukung: JPG, PNG.@if ($editing) Memilih foto baru pada foto yang sudah ada akan menggantinya.@endif
            </p>
        </section>

        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.vehicles.index') }}" class="rounded-lg border border-outline-variant px-6 py-3 text-center text-label-md text-on-surface-variant transition-colors hover:bg-surface-container-low">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-navy px-8 py-3 text-label-md text-white shadow-[0_4px_6px_-4px_rgba(0,42,93,.2),0_10px_15px_-3px_rgba(0,42,93,.2)] transition-all hover:brightness-110">
                <span class="material-symbols-outlined text-[18px]">save</span>{{ $editing ? 'Simpan Perubahan' : 'Simpan Kendaraan' }}
            </button>
        </div>
    </form>

    {{-- Forms for "Hapus" photo buttons: nested forms are not allowed, so the buttons point here via the form attribute. --}}
    @if ($editing)
        @foreach (\App\Enums\PhotoSide::cases() as $side)
            @if ($vehicle->photoFor($side))
                <form id="delete-photo-{{ $side->value }}" method="POST" action="{{ route('admin.vehicles.photos.destroy', [$vehicle, $side->value]) }}"
                      data-confirm-title="Hapus Foto" data-confirm-ok="Ya, Hapus" data-confirm-message="Hapus {{ strtolower($side->label()) }} kendaraan ini?">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        @endforeach
    @endif
</x-admin.layout>
