<x-layouts.app>
    {{-- Hero --}}
    <section class="relative flex min-h-[70vh] items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 z-10 bg-gradient-to-r from-background via-background/90 to-transparent"></div>
            <img class="h-full w-full object-cover" alt="Ambulans dinas kesehatan terparkir" src="{{ asset('images/hero.png') }}">
        </div>
        <div class="relative z-20 mx-auto grid max-w-container-max gap-12 px-margin-mobile md:grid-cols-2 md:px-margin-desktop">
            <div class="flex flex-col justify-center gap-stack-lg">
                <div>
                    <span class="mb-stack-sm inline-block rounded-sm bg-secondary-container px-3 py-1 text-label-sm text-on-secondary-container">LAYANAN INTERNAL</span>
                    <h1 class="mb-4 text-headline-xl text-on-background">Peminjaman Kendaraan Dinas Jadi Lebih Mudah.</h1>
                    <p class="max-w-lg text-body-lg text-on-surface-variant">
                        Optimalkan mobilitas operasional Anda dengan sistem pemesanan armada yang terintegrasi, transparan, dan efisien untuk staf Dinas Kesehatan PPKB Purbalingga.
                    </p>
                </div>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('katalog') }}" class="flex items-center gap-2 rounded-xl bg-primary-container px-8 py-4 text-headline-sm text-on-primary transition-transform hover:scale-105">
                        Pinjam Sekarang <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="{{ route('status.index') }}" class="flex items-center gap-2 rounded-xl border border-primary px-8 py-4 text-headline-sm text-primary transition-all hover:bg-primary hover:text-white">
                        Cek Status Peminjaman <span class="material-symbols-outlined">history</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Proses peminjaman --}}
    <section class="bg-background py-stack-lg">
        <div class="mx-auto max-w-container-max px-margin-mobile md:px-margin-desktop">
            <div class="mb-stack-lg text-center">
                <h2 class="text-headline-lg text-on-background">Proses Peminjaman Sederhana</h2>
                <p class="text-body-md text-on-surface-variant">Hanya butuh 3 langkah mudah untuk mendapatkan akses kendaraan.</p>
            </div>
            <div class="grid gap-gutter md:grid-cols-3">
                @foreach ([
                    ['search', '1. Pilih Armada', 'Telusuri katalog kendaraan dinas yang tersedia sesuai dengan kebutuhan operasional Anda.', '01'],
                    ['event_available', '2. Isi Jadwal', 'Tentukan tanggal pemakaian dan tujuan perjalanan dinas melalui formulir digital yang ringkas.', '02'],
                    ['key', '3. Ambil Kunci', 'Dapatkan verifikasi admin dan ambil kunci kendaraan di bagian logistik kantor Dinkes PPKB.', '03'],
                ] as [$icon, $title, $text, $number])
                    <div class="group relative border border-outline-variant bg-surface p-10 transition-all duration-300 hover:border-primary">
                        <div class="mb-stack-md flex h-16 w-16 items-center justify-center rounded-full bg-primary-container/10 text-primary-container transition-transform group-hover:scale-110">
                            <span class="material-symbols-outlined text-4xl">{{ $icon }}</span>
                        </div>
                        <h3 class="mb-2 text-headline-sm">{{ $title }}</h3>
                        <p class="text-body-md text-on-surface-variant">{{ $text }}</p>
                        <div class="absolute right-4 top-4 select-none text-6xl font-bold text-primary/10">{{ $number }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Jadwal penggunaan kendaraan --}}
    <section class="bg-surface-container-low py-stack-lg">
        <div class="mx-auto max-w-container-max px-margin-mobile md:px-margin-desktop">
            <div class="mb-stack-lg text-center">
                <h2 class="text-headline-lg text-on-background">Jadwal Penggunaan Kendaraan</h2>
                <p class="text-body-md text-on-surface-variant">Pantau ketersediaan seluruh armada secara real-time untuk merencanakan perjalanan dinas Anda.</p>
            </div>

            <div id="calendar" data-month-url="{{ route('calendar.month') }}" data-day-url="{{ route('calendar.day') }}" data-today="{{ today()->toDateString() }}">
                <div class="overflow-hidden rounded-xl border border-outline-variant bg-surface shadow-sm">
                    <div class="flex items-center justify-between border-b border-outline-variant bg-surface-container-lowest p-6">
                        <h3 class="text-headline-sm text-primary" data-calendar-label></h3>
                        <div class="flex gap-2">
                            <button type="button" data-calendar-prev aria-label="Bulan sebelumnya" class="rounded-lg border border-outline-variant p-1.5 transition-colors hover:bg-surface-container-low"><span class="material-symbols-outlined">chevron_left</span></button>
                            <button type="button" data-calendar-next aria-label="Bulan berikutnya" class="rounded-lg border border-outline-variant p-1.5 transition-colors hover:bg-surface-container-low"><span class="material-symbols-outlined">chevron_right</span></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 border-b border-outline-variant bg-surface-container-low">
                        @foreach (['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'] as $day)
                            <div class="py-3 text-center text-label-sm text-on-surface-variant">{{ $day }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7" data-calendar-grid></div>
                    <div class="flex flex-col items-center justify-between gap-4 bg-surface-container-lowest p-4 sm:flex-row">
                        <div class="flex gap-4">
                            <div class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-primary"></span><span class="text-[10px] font-medium text-on-surface-variant">Dipesan</span></div>
                            <div class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-500"></span><span class="text-[10px] font-medium text-on-surface-variant">Perawatan</span></div>
                        </div>
                    </div>
                </div>

                {{-- Booking detail for the clicked day --}}
                <div data-calendar-modal class="fixed inset-0 z-[65] hidden items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true" aria-labelledby="calendar-modal-title">
                    <div class="flex max-h-[85vh] w-full max-w-3xl flex-col rounded-xl border border-outline-variant bg-surface shadow-lg">
                        <div class="flex items-start justify-between gap-4 border-b border-outline-variant p-6">
                            <div>
                                <h3 id="calendar-modal-title" class="flex items-center gap-2 text-headline-sm text-on-background">
                                    <span class="material-symbols-outlined text-primary">event_note</span>
                                    Detail Jadwal Armada
                                </h3>
                                <p class="mt-1 text-body-sm text-on-surface-variant" data-calendar-modal-date></p>
                            </div>
                            <button type="button" data-calendar-modal-close aria-label="Tutup" class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container-low">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="grid gap-gutter overflow-y-auto p-6 md:grid-cols-2" data-calendar-detail aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Katalog armada (pratinjau) --}}
    <section class="bg-surface-container-lowest py-stack-lg">
        <div class="mx-auto max-w-container-max px-margin-mobile md:px-margin-desktop">
            <div class="mb-stack-lg flex items-end justify-between">
                <div>
                    <h2 class="text-headline-lg text-on-background">Katalog Armada</h2>
                    <p class="text-body-md text-on-surface-variant">Daftar kendaraan operasional dalam kondisi prima.</p>
                </div>
                <a href="{{ route('katalog') }}" class="flex items-center gap-1 text-label-md text-primary hover:underline">Lihat Semua <span class="material-symbols-outlined">chevron_right</span></a>
            </div>
            <div class="grid gap-gutter md:grid-cols-3">
                @foreach ($vehicles as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" compact />
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
