<x-admin.layout title="Beranda">
    <x-admin.page-header title="Beranda" subtitle="Selamat datang kembali, {{ auth()->user()->name }}. Berikut status armada hari ini." />

    {{-- Statistik --}}
    <div class="mb-stack-lg grid grid-cols-1 gap-gutter sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total Armada" :value="$stats['total']" icon="directions_car" tone="navy" />
        <x-admin.stat-card label="Tersedia" :value="$stats['available']" icon="check_circle" tone="green" hint="Hari ini" />
        <x-admin.stat-card label="Menunggu" :value="$stats['pending']" icon="hourglass_top" tone="amber" hint="Butuh verifikasi" hintTone="amber" />
        <x-admin.stat-card label="Jadwal Hari Ini" :value="$stats['today']" icon="event_available" tone="blue" />
    </div>

    {{-- Grafik kendaraan paling sering digunakan --}}
    <section class="mb-stack-lg rounded-xl border border-outline-variant bg-white/70 p-5 shadow-sm sm:p-6" aria-labelledby="chart-title">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 id="chart-title" class="text-headline-sm text-navy">Kendaraan Paling Sering Digunakan</h2>
                <p class="text-label-sm text-on-surface-variant">Jumlah pengajuan disetujui pada bulan terpilih</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard', ['bulan' => $previousMonth]) }}" aria-label="Bulan sebelumnya" class="rounded-lg border border-outline-variant p-1.5 transition-colors hover:bg-surface-container-low"><span class="material-symbols-outlined">chevron_left</span></a>
                <span class="min-w-[9rem] rounded-full bg-surface-container px-4 py-1.5 text-center text-label-md text-on-surface-variant">{{ $month->translatedFormat('F Y') }}</span>
                <a href="{{ route('admin.dashboard', ['bulan' => $nextMonth]) }}" aria-label="Bulan berikutnya" class="rounded-lg border border-outline-variant p-1.5 transition-colors hover:bg-surface-container-low"><span class="material-symbols-outlined">chevron_right</span></a>
            </div>
        </div>

        @if (count($chart) === 0)
            <div class="rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest py-stack-lg text-center text-body-sm text-on-surface-variant">
                Belum ada pengajuan disetujui pada {{ $month->translatedFormat('F Y') }}.
            </div>
        @else
            @php $max = max(array_column($chart, 'total')); @endphp
            {{-- Bar height is proportional to the value (Figma's mock used equal heights). --}}
            <div class="flex h-64 items-end justify-around gap-2 border-b border-outline-variant px-2 sm:gap-6" role="img" aria-label="Grafik batang jumlah pengajuan per kendaraan">
                @foreach ($chart as $row)
                    {{-- The value sits inside the bar (Figma); the minimum height keeps it readable for small values. --}}
                    <span class="flex w-16 items-start justify-center rounded-t-lg bg-navy pt-1.5 text-label-md text-white shadow-sm sm:w-20"
                          style="height: {{ max(14, round($row['total'] / $max * 100)) }}%" data-chart-bar data-value="{{ $row['total'] }}">{{ $row['total'] }}</span>
                @endforeach
            </div>
            <div class="flex justify-around gap-2 px-2 pt-3 sm:gap-6">
                @foreach ($chart as $row)
                    <div class="w-16 text-center sm:w-20">
                        <p class="truncate text-label-md text-on-surface" title="{{ $row['vehicle']->name }}">{{ $row['vehicle']->name }}</p>
                        <p class="text-[10px] uppercase tracking-wide text-on-surface-variant">{{ $row['vehicle']->plate }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Kendaraan yang digunakan hari ini --}}
    <section aria-labelledby="today-title">
        <div class="mb-stack-md">
            <h2 id="today-title" class="flex items-center gap-2 text-headline-sm text-navy">
                <span class="material-symbols-outlined">event_note</span> Kendaraan Digunakan Hari Ini
            </h2>
            <p class="text-body-sm text-on-surface-variant">{{ $today->translatedFormat('l, j F Y') }}</p>
        </div>

        @if ($usedToday->isEmpty())
            <div class="rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest py-stack-lg text-center text-body-sm text-on-surface-variant">
                Tidak ada kendaraan yang digunakan hari ini.
            </div>
        @else
            <div class="grid grid-cols-1 gap-gutter md:grid-cols-2">
                @foreach ($usedToday as $booking)
                    <article class="rounded-xl border border-outline-variant bg-white p-5 shadow-sm">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-headline-sm font-bold leading-6 text-navy">{{ $booking->vehicle->name }}</h3>
                                <p class="text-label-sm text-on-surface-variant">Plat No: {{ $booking->vehicle->plate }}</p>
                            </div>
                            <span class="whitespace-nowrap rounded-sm bg-success/10 px-2 py-1 text-[10px] font-bold uppercase text-success">Disetujui</span>
                        </div>
                        <hr class="mb-3 border-outline-variant">
                        <div class="space-y-2 text-body-sm">
                            <p class="flex items-start gap-2 text-on-surface">
                                <span class="material-symbols-outlined mt-0.5 text-[18px] text-navy">person</span>
                                <span><strong>{{ $booking->employee->name }}</strong> <span class="text-on-surface-variant">NIP: {{ $booking->employee->nip }}</span><br><span class="text-on-surface-variant">Bidang: {{ $booking->employee->bidangShort() }}</span></span>
                            </p>
                            <p class="flex items-center gap-2 text-on-surface-variant"><span class="material-symbols-outlined text-[18px]">calendar_today</span>{{ $booking->scheduleLabel() }}</p>
                            <p class="flex items-center gap-2 font-semibold text-on-surface"><span class="material-symbols-outlined text-[18px] text-error">location_on</span>{{ $booking->destination }}</p>
                            <p class="italic text-on-surface-variant">"{{ $booking->purpose }}"</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-admin.layout>
