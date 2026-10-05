<x-admin.layout title="Manajemen Admin">
    <x-admin.page-header title="Manajemen Admin" subtitle="Buat dan kelola akun administrator yang dapat masuk ke panel ini.">
        <x-slot:actions>
            <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-navy px-6 py-3 text-label-md text-white shadow-[0_4px_6px_-4px_rgba(0,42,93,.2),0_10px_15px_-3px_rgba(0,42,93,.2)] transition-all hover:brightness-110">
                <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span> Tambah Admin
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-stack-lg grid grid-cols-1 gap-gutter sm:grid-cols-3">
        <x-admin.stat-card label="Total Admin" :value="$stats['total']" icon="admin_panel_settings" tone="navy" />
        <x-admin.stat-card label="Aktif" :value="$stats['active']" icon="check_circle" tone="green" />
        <x-admin.stat-card label="Nonaktif" :value="$stats['inactive']" icon="block" tone="amber" />
    </div>

    <form method="GET" action="{{ route('admin.admins.index') }}" role="search" data-autosubmit class="mb-stack-md">
        <label class="flex items-center gap-3 rounded-xl border border-outline-variant bg-white px-4 py-3">
            <span class="material-symbols-outlined text-outline">search</span>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Cari nama, NIP, atau email..." aria-label="Cari administrator"
                   class="w-full border-0 bg-transparent p-0 text-body-sm focus:ring-0">
        </label>
    </form>

    <div class="overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left md:min-w-[40rem]">
                <thead class="max-md:hidden bg-surface-container-low text-label-sm uppercase tracking-wide text-on-surface-variant">
                    <tr>
                        <th scope="col" class="px-5 py-3">Administrator</th>
                        <th scope="col" class="px-5 py-3">NIP</th>
                        <th scope="col" class="px-5 py-3">Bidang</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="max-md:block divide-y divide-outline-variant">
                    @foreach ($admins as $admin)
                        @php $isSelf = $admin->is(auth()->user()); @endphp
                        <tr class="hover:bg-surface-container-lowest max-md:block max-md:p-4">
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-secondary-fixed text-label-md text-navy">{{ $admin->initials() }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-label-md text-on-background">{{ $admin->name }}@if ($isSelf) <span class="ml-1 rounded bg-surface-container px-1.5 py-0.5 text-label-sm text-on-surface-variant">Anda</span>@endif</p>
                                        <p class="truncate text-label-sm text-on-surface-variant">{{ $admin->email ?: '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4 text-body-sm tabular-nums text-on-surface-variant">{{ $admin->nip }}</td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4 text-body-sm text-on-background">{{ $admin->bidang ?: '-' }}</td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <span @class(['inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-label-sm', 'bg-[#e6f4ea] text-green-700' => $admin->is_active, 'bg-surface-container text-on-surface-variant' => ! $admin->is_active])>
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $admin->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.admins.edit', $admin) }}" aria-label="Ubah {{ $admin->name }}" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-navy">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    @unless ($isSelf)
                                        <form method="POST" action="{{ route('admin.admins.toggle', $admin) }}"
                                              data-confirm-title="{{ $admin->is_active ? 'Nonaktifkan Administrator' : 'Aktifkan Administrator' }}"
                                              data-confirm-ok="{{ $admin->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                              @unless ($admin->is_active) data-confirm-tone="primary" @endunless
                                              data-confirm-message="{{ $admin->is_active ? 'Administrator ini tidak akan bisa masuk ke panel.' : 'Administrator ini bisa kembali masuk ke panel.' }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" aria-label="{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $admin->name }}" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-navy">
                                                <span class="material-symbols-outlined text-[20px]">{{ $admin->is_active ? 'lock' : 'lock_open' }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                              data-confirm-title="Hapus Administrator" data-confirm-ok="Ya, Hapus"
                                              data-confirm-message="Hapus akun {{ $admin->name }}? Tindakan ini tidak dapat dibatalkan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Hapus {{ $admin->name }}" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-error-container hover:text-error">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $admins->links('vendor.pagination.admin', ['unit' => 'administrator']) }}
</x-admin.layout>
