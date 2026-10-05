@php
    $editing = $employee->exists;
    $action = $editing ? route('admin.employees.update', $employee) : route('admin.employees.store');
@endphp

<x-admin.layout :title="$editing ? 'Ubah Pegawai' : 'Tambah Pegawai'">
    <nav class="mb-3 flex items-center gap-2 text-label-sm text-on-surface-variant" aria-label="Breadcrumb">
        <a href="{{ route('admin.employees.index') }}" class="hover:text-navy hover:underline">Manajemen Pegawai</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-bold text-navy">{{ $editing ? 'Ubah Pegawai' : 'Tambah Pegawai' }}</span>
    </nav>

    <x-admin.page-header :title="$editing ? 'Ubah Data Pegawai' : 'Tambah Pegawai Baru'" subtitle="NIP dipakai pegawai untuk memverifikasi diri saat mengajukan peminjaman." />

    <form method="POST" action="{{ $action }}" class="space-y-stack-lg" novalidate>
        @csrf
        @if ($editing) @method('PUT') @endif

        <x-admin.form-section title="Data Pegawai" icon="badge">
            <x-admin.field name="nip" label="NIP" :value="$employee->nip" placeholder="18 digit angka" maxlength="22" inputmode="numeric" required />
            <x-admin.field name="name" label="Nama Lengkap" :value="$employee->name" maxlength="255" required />
            <x-admin.field name="email" label="Email" type="email" :value="$employee->email" placeholder="nama@purbalinggakab.go.id" maxlength="255" />
            <x-admin.field name="bidang" label="Bidang / Unit Kerja" :value="$employee->bidang" list="bidang-options" placeholder="Contoh: Sekretariat" maxlength="100" required />
            <x-admin.field name="jabatan" label="Jabatan" :value="$employee->jabatan" maxlength="150" />
            <x-admin.field name="pangkat" label="Pangkat / Golongan" :value="$employee->pangkat" placeholder="Contoh: Penata / IIIc" maxlength="100" />
            <datalist id="bidang-options">
                @foreach ($bidangOptions as $bidang)
                    <option value="{{ $bidang }}"></option>
                @endforeach
            </datalist>

            @if ($editing)
                <label class="flex items-center gap-3 md:col-span-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employee->is_active)) class="h-5 w-5 rounded border-outline-variant text-navy focus:ring-navy/30">
                    <span class="text-label-md text-on-surface-variant">Pegawai aktif (dapat mengajukan peminjaman)</span>
                </label>
            @endif
        </x-admin.form-section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.employees.index') }}" class="rounded-lg border border-outline-variant px-6 py-3 text-center text-label-md text-on-surface-variant transition-colors hover:bg-surface-container-low">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-navy px-8 py-3 text-label-md text-white transition-all hover:brightness-110">
                <span class="material-symbols-outlined text-[18px]">save</span>{{ $editing ? 'Simpan Perubahan' : 'Simpan Pegawai' }}
            </button>
        </div>
    </form>
</x-admin.layout>
