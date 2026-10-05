@php
    $editing = $admin->exists;
    $action = $editing ? route('admin.admins.update', $admin) : route('admin.admins.store');
@endphp

<x-admin.layout :title="$editing ? 'Ubah Administrator' : 'Tambah Administrator'">
    <nav class="mb-3 flex items-center gap-2 text-label-sm text-on-surface-variant" aria-label="Breadcrumb">
        <a href="{{ route('admin.admins.index') }}" class="hover:text-navy hover:underline">Manajemen Admin</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-bold text-navy">{{ $editing ? 'Ubah Administrator' : 'Tambah Administrator' }}</span>
    </nav>

    <x-admin.page-header :title="$editing ? 'Ubah Akun Administrator' : 'Tambah Administrator Baru'" subtitle="Administrator masuk menggunakan NIP sebagai ID Administrator dan kata sandi." />

    <form method="POST" action="{{ $action }}" class="space-y-stack-lg" novalidate autocomplete="off">
        @csrf
        @if ($editing) @method('PUT') @endif

        <x-admin.form-section title="Data Administrator" icon="badge">
            <x-admin.field name="nip" label="NIP (ID Administrator)" :value="$admin->nip" placeholder="18 digit angka" maxlength="22" inputmode="numeric" required />
            <x-admin.field name="name" label="Nama Lengkap" :value="$admin->name" maxlength="255" required />
            <x-admin.field name="bidang" label="Bidang" :value="$admin->bidang" maxlength="100" />
            <x-admin.field name="email" label="Email" type="email" :value="$admin->email" maxlength="255" />
        </x-admin.form-section>

        <x-admin.form-section title="Kata Sandi" icon="lock" tone="soft">
            <x-admin.field name="password" label="{{ $editing ? 'Kata Sandi Baru' : 'Kata Sandi' }}" type="password" :required="! $editing"
                           :hint="$editing ? 'Kosongkan bila tidak ingin mengubah kata sandi.' : 'Minimal 8 karakter, mengandung angka dan simbol.'" />
            <x-admin.field name="password_confirmation" label="Konfirmasi Kata Sandi" type="password" :required="! $editing" />
        </x-admin.form-section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.admins.index') }}" class="rounded-lg border border-outline-variant px-6 py-3 text-center text-label-md text-on-surface-variant transition-colors hover:bg-surface-container-low">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-navy px-8 py-3 text-label-md text-white transition-all hover:brightness-110">
                <span class="material-symbols-outlined text-[18px]">save</span>{{ $editing ? 'Simpan Perubahan' : 'Buat Akun' }}
            </button>
        </div>
    </form>
</x-admin.layout>
