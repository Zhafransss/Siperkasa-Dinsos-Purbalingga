<x-layouts.app title="Status Peminjaman">
    <div class="mx-auto w-full max-w-container-max px-margin-mobile pb-stack-lg pt-stack-lg md:px-margin-desktop">
        <div class="mb-stack-lg">
            <h1 class="mb-2 text-headline-lg text-on-background">Daftar Peminjaman Kendaraan</h1>
            <p class="text-body-md text-on-surface-variant">Pantau status pengajuan kendaraan dinas Anda secara real-time.</p>
        </div>

        @if (! $employee)
            {{-- No verified NIP in this browser session yet. --}}
            <div class="mx-auto max-w-md rounded-xl border border-outline-variant bg-surface-container-lowest p-stack-lg">
                <div class="mb-stack-md flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#dcfce7]"><span class="material-symbols-outlined text-4xl text-[#15803d]">verified_user</span></div>
                </div>
                <h2 class="mb-2 text-center text-headline-md text-on-background">Masukkan NIP Anda</h2>
                <p class="mb-stack-md text-center text-body-md text-on-surface-variant">Masukkan NIP Pegawai Anda untuk melihat riwayat dan status pengajuan.</p>

                <form method="POST" action="{{ route('status.login') }}" class="flex flex-col gap-stack-sm">
                    @csrf
                    @if ($errors->has('nip'))
                        <div class="mb-2 flex items-start gap-3 rounded-xl border border-error bg-error-container p-4 text-error" role="alert">
                            <span class="material-symbols-outlined mt-0.5">error</span>
                            <p class="text-body-sm font-medium">{{ $errors->first('nip') }}</p>
                        </div>
                    @endif
                    <label for="nip" class="text-label-md font-bold text-on-surface">NIP (Nomor Induk Pegawai) 18 Digit *</label>
                    <input id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" autocomplete="off" value="{{ old('nip') }}"
                           placeholder="Contoh: 198811042012021001"
                           class="h-12 w-full rounded-lg border border-outline-variant px-4 transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <button type="submit" class="mt-stack-sm h-12 rounded-lg bg-primary px-8 text-label-md text-white transition-all hover:bg-primary-container">Lihat Status</button>
                </form>
            </div>
        @else
            <div class="mb-stack-md flex flex-wrap items-center justify-between gap-2 text-body-sm text-on-surface-variant">
                <span>Masuk sebagai <strong class="text-on-surface">{{ $employee->name }}</strong> · NIP {{ $employee->nip }}</span>
                <form method="POST" action="{{ route('status.logout') }}">
                    @csrf
                    <button type="submit" class="text-primary hover:underline">Bukan Anda? Ganti NIP</button>
                </form>
            </div>

            {{-- GET form: the search runs on the server over all pages and survives pagination via the query string. --}}
            <form method="GET" action="{{ route('status.index') }}" role="search" class="mb-stack-md flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div class="relative w-full md:w-[30rem]">
                    <button type="submit" aria-label="Cari" class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                    <input type="text" name="q" value="{{ $search }}" maxlength="100" autocomplete="off"
                           placeholder="Cari NIP, nama, kendaraan, kode booking, atau tujuan…" aria-label="Cari pengajuan"
                           class="w-full rounded-lg border border-outline-variant bg-white py-2 pl-10 pr-10 text-body-sm transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                    @if ($search !== '')
                        <a href="{{ route('status.index') }}" aria-label="Hapus pencarian" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                            <span class="material-symbols-outlined">close</span>
                        </a>
                    @endif
                </div>
                <span class="text-body-sm text-on-surface-variant">
                    @if ($bookings->total() > 0)
                        Menampilkan {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} dari {{ $bookings->total() }} Pengajuan Peminjaman
                    @else
                        Menampilkan 0 Pengajuan Peminjaman
                    @endif
                </span>
            </form>

            <div class="space-y-gutter" data-status-list>
                @forelse ($bookings as $booking)
                    {{-- $status is the stored status (drives notes/cancel); $shown adds the derived "Selesai". --}}
                    @php $status = $booking->status; $shown = $booking->effectiveStatus(); @endphp
                    <div class="overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm transition-all hover:shadow-md">
                        <div class="p-6">
                            <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-outline-variant pb-4">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[12px] font-bold {{ $shown->badgeClasses() }}">
                                        <span class="material-symbols-outlined text-[16px]">{{ $shown->icon() }}</span> {{ $shown->label() }}
                                    </span>
                                    <span class="text-label-md text-on-surface-variant">Kode: <span class="font-bold text-on-background">{{ $booking->code }}</span></span>
                                </div>
                                <span class="text-label-sm text-on-surface-variant">Diajukan pada: {{ $booking->created_at->format('Y-m-d H:i') }}</span>
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-8 md:grid-cols-3">
                                <div>
                                    <p class="mb-2 text-label-sm uppercase text-outline">Kendaraan</p>
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined mt-1 text-primary">directions_car</span>
                                        <div>
                                            <h4 class="text-[18px] font-semibold leading-tight">{{ $booking->vehicle->name }}</h4>
                                            <span class="mt-2 inline-block rounded-sm bg-surface-container px-2 py-0.5 font-mono text-[13px] text-on-surface-variant">Plat No: {{ $booking->vehicle->plate }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-2 text-label-sm uppercase text-outline">Pemohon</p>
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined mt-1 text-primary">person</span>
                                        <div>
                                            <h4 class="text-[18px] font-semibold leading-tight">{{ $employee->name }}</h4>
                                            <span class="mt-2 inline-block rounded-sm bg-surface-container px-2 py-0.5 font-mono text-[13px] text-on-surface-variant">NIP: {{ $employee->nip }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-2 text-label-sm uppercase text-outline">Waktu &amp; Tujuan</p>
                                    <h4 class="text-[18px] font-semibold leading-tight">{{ $booking->destination }}</h4>
                                    <div class="mt-2 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                        <span class="text-[14px]">{{ $booking->shortScheduleLabel() }}</span>
                                    </div>
                                </div>
                            </div>

                            @if ($status === \App\Enums\BookingStatus::Disetujui && $booking->admin_note)
                                <div class="rounded-lg border border-green-700/20 bg-[#e6f4ea] p-4 text-green-700">
                                    <h5 class="mb-1 flex items-center gap-2 text-label-md font-semibold">Catatan Pengelola Dinkes:</h5>
                                    <p class="text-body-sm italic">{{ $booking->admin_note }}</p>
                                </div>
                            @elseif ($status === \App\Enums\BookingStatus::Ditolak && $booking->admin_note)
                                <div class="rounded-lg border border-red-700/20 bg-[#fce8e8] p-4 text-red-700">
                                    <h5 class="mb-1 flex items-center gap-2 text-label-md font-semibold"><span class="material-symbols-outlined text-[18px]">error</span>Alasan Penolakan Pengelola Admin:</h5>
                                    <p class="text-body-sm italic">{{ $booking->admin_note }}</p>
                                </div>
                            @elseif ($booking->isCancellable())
                                <div class="flex items-center justify-end pt-2">
                                    <form method="POST" action="{{ route('booking.cancel', $booking) }}"
                                          data-confirm-title="Batalkan Peminjaman"
                                          data-confirm-message="Apakah Anda yakin ingin membatalkan peminjaman ini?"
                                          data-confirm-ok="Ya, Batalkan">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg px-4 py-2 text-label-md font-bold text-error transition-colors hover:bg-error/5">Batalkan Peminjaman</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest py-stack-lg text-center text-body-sm text-on-surface-variant">
                        @if ($search !== '')
                            Tidak ada pengajuan yang cocok dengan pencarian "{{ $search }}".
                            <a href="{{ route('status.index') }}" class="text-primary hover:underline">Hapus pencarian</a>
                        @else
                            Anda belum memiliki pengajuan peminjaman. <a href="{{ route('katalog') }}" class="text-primary hover:underline">Lihat Katalog Mobil</a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($bookings->hasPages())
                {{ $bookings->links() }}
            @endif
        @endif
    </div>
</x-layouts.app>
