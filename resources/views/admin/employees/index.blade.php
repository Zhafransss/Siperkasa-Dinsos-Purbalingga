<x-admin.layout title="Manajemen Pegawai">
    <x-admin.page-header title="Manajemen Pegawai" subtitle="Kelola data pegawai yang berhak mengajukan peminjaman kendaraan dinas.">
        <x-slot:actions>
            <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-navy px-6 py-3 text-label-md text-white shadow-[0_4px_6px_-4px_rgba(0,42,93,.2),0_10px_15px_-3px_rgba(0,42,93,.2)] transition-all hover:brightness-110">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Pegawai
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-stack-lg grid grid-cols-1 gap-gutter sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total Pegawai" :value="$stats['total']" icon="groups" tone="navy" />
        <x-admin.stat-card label="Aktif" :value="$stats['active']" icon="check_circle" tone="green" />
        <x-admin.stat-card label="Nonaktif" :value="$stats['inactive']" icon="person_off" tone="amber" />
        <x-admin.stat-card label="Unit Kerja" :value="$stats['units']" icon="apartment" tone="blue" />
    </div>

    <form method="GET" action="{{ route('admin.employees.index') }}" role="search" data-autosubmit class="mb-stack-md grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto]">
        <label class="flex items-center gap-3 rounded-xl border border-outline-variant bg-white px-4 py-3">
            <span class="material-symbols-outlined text-outline">search</span>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Cari nama, NIP, atau email..." aria-label="Cari pegawai"
                   class="w-full border-0 bg-transparent p-0 text-body-sm focus:ring-0">
        </label>
        <select name="bidang" aria-label="Filter bidang" class="rounded-xl border border-outline-variant bg-white px-4 py-3 text-label-md text-on-surface-variant focus:border-navy focus:ring-2 focus:ring-navy/20">
            <option value="">Bidang: Semua</option>
            @foreach ($bidangOptions as $bidang)
                <option value="{{ $bidang }}" @selected(($filters['bidang'] ?? null) === $bidang)>{{ $bidang }}</option>
            @endforeach
        </select>
    </form>

    <div class="overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left md:min-w-[44rem]">
                <thead class="max-md:hidden bg-surface-container-low text-label-sm uppercase tracking-wide text-on-surface-variant">
                    <tr>
                        <th scope="col" class="px-5 py-3">Pegawai</th>
                        <th scope="col" class="px-5 py-3">NIP</th>
                        <th scope="col" class="px-5 py-3">Bidang / Jabatan</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="max-md:block divide-y divide-outline-variant">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-surface-container-lowest max-md:block max-md:p-4">
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-secondary-fixed text-label-md text-navy">{{ $employee->initials() }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-label-md text-on-background">{{ $employee->name }}</p>
                                        <p class="truncate text-label-sm text-on-surface-variant">{{ $employee->email ?: '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4 text-body-sm tabular-nums text-on-surface-variant">{{ $employee->nip }}</td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <p class="text-body-sm text-on-background">{{ $employee->bidang }}</p>
                                <p class="text-label-sm text-on-surface-variant">{{ $employee->jabatan ?: '-' }}</p>
                            </td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <span @class(['inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-label-sm', 'bg-[#e6f4ea] text-green-700' => $employee->is_active, 'bg-surface-container text-on-surface-variant' => ! $employee->is_active])>
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="max-md:block max-md:px-0 max-md:py-1.5 px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" aria-label="Ubah {{ $employee->name }}" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-navy">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.employees.toggle', $employee) }}"
                                          data-confirm-title="{{ $employee->is_active ? 'Nonaktifkan Pegawai' : 'Aktifkan Pegawai' }}"
                                          data-confirm-ok="{{ $employee->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                          @unless ($employee->is_active) data-confirm-tone="primary" @endunless
                                          data-confirm-message="{{ $employee->is_active ? 'Pegawai ini tidak akan bisa mengajukan peminjaman baru.' : 'Pegawai ini bisa kembali mengajukan peminjaman.' }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" aria-label="{{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $employee->name }}" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-navy">
                                            <span class="material-symbols-outlined text-[20px]">{{ $employee->is_active ? 'person_off' : 'person_check' }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}"
                                          data-confirm-title="Hapus Pegawai" data-confirm-ok="Ya, Hapus"
                                          data-confirm-message="Hapus {{ $employee->name }}? Tindakan ini tidak dapat dibatalkan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Hapus {{ $employee->name }}" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-error-container hover:text-error">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-stack-lg text-center text-body-sm text-on-surface-variant">Tidak ada pegawai yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $employees->links('vendor.pagination.admin', ['unit' => 'pegawai']) }}
</x-admin.layout>
