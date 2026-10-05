<x-layouts.app title="Pengajuan Berhasil">
    {{-- Compact on purpose: the whole confirmation (illustration, message, booking card, buttons) fits one screen. --}}
    <div class="mx-auto flex max-w-3xl flex-col items-center px-margin-mobile pb-16 pt-stack-md text-center md:px-margin-desktop">
        <img class="mb-3 h-[112px] w-[118px]" alt="Pengajuan berhasil" src="{{ asset('images/ilustrasi-sukses.svg') }}">

        <h1 class="mb-2 text-headline-lg-mobile text-on-surface md:text-headline-lg">Pengajuan Berhasil Terkirim!</h1>
        <p class="mb-stack-md max-w-2xl text-body-md text-on-surface-variant">
            Terima kasih telah menggunakan layanan peminjaman kendaraan operasional. Tim administrasi kami akan segera meninjau permohonan Anda. Anda dapat memantau progres pengajuan melalui halaman Status Peminjaman.
        </p>

        <div class="mb-stack-md w-full max-w-2xl rounded-xl border border-outline-variant bg-surface-container-lowest p-5 text-left shadow-sm">
            <div class="mb-4 flex flex-col items-start justify-between gap-2 border-b border-outline-variant pb-3 sm:flex-row sm:items-center">
                <div>
                    <p class="text-label-sm uppercase tracking-wider text-on-surface-variant">Booking ID</p>
                    <p class="text-headline-sm text-primary">{{ $booking->displayId() }}</p>
                </div>
                <span class="rounded-full bg-secondary-container px-3 py-1 text-label-sm font-medium text-on-secondary-container">Menunggu Verifikasi</span>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div class="flex items-start gap-3">
                    <div class="rounded-lg bg-surface-container-high p-2 text-primary"><span class="material-symbols-outlined">badge</span></div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant">Identitas Pemohon</p>
                        <p class="text-body-md font-semibold text-on-surface">{{ $booking->employee->name }}</p>
                        <p class="text-body-sm text-on-surface-variant">NIP: {{ $booking->employee->nip }} • Bidang: {{ $booking->employee->bidangShort() }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="rounded-lg bg-surface-container-high p-2 text-primary"><span class="material-symbols-outlined">directions_car</span></div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant">Tipe Kendaraan</p>
                        <p class="text-body-md font-semibold text-on-surface">{{ $booking->vehicle->name }} ({{ $booking->vehicle->plate }})</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="rounded-lg bg-surface-container-high p-2 text-primary"><span class="material-symbols-outlined">calendar_today</span></div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant">Waktu Peminjaman</p>
                        <p class="text-body-md font-semibold text-on-surface">{{ $booking->scheduleLabel() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('status.index') }}" class="rounded-lg bg-primary-container px-stack-lg py-3 text-center text-label-md text-white shadow-sm transition-all hover:brightness-110 active:scale-95">Lihat Status Peminjaman</a>
            <a href="{{ route('home') }}" class="rounded-lg border border-primary px-stack-lg py-3 text-center text-label-md text-primary transition-all hover:bg-surface-container-low active:scale-95">Kembali ke Beranda</a>
        </div>
    </div>
</x-layouts.app>
