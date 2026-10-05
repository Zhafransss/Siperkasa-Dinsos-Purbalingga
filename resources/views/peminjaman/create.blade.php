@php
    // Re-open the wizard on the step that holds the server-side error.
    $initialStep = $errors->has('nip') ? 1 : ($errors->any() ? 2 : 1);
    $stepErrors = collect(['vehicle_id', 'departs_on', 'returns_on', 'destination', 'purpose'])
        ->flatMap(fn ($field) => $errors->get($field));
    $earliest = \App\Services\BookingWindow::earliestDeparture();
    $earliestLabel = $earliest->translatedFormat('l, j F Y');
    $inputBase ='w-full h-12 border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all';
@endphp

<x-layouts.app title="Formulir Peminjaman">
    <div class="mx-auto w-full max-w-container-max px-margin-mobile pb-stack-lg pt-stack-md md:px-margin-desktop"
         id="booking-wizard"
         data-initial-step="{{ $initialStep }}"
         data-verify-url="{{ route('booking.verify-nip') }}"
         data-employee-name="{{ $employeeName }}"
         data-earliest="{{ $earliest->toDateString() }}"
         data-earliest-label="{{ $earliestLabel }}"
         data-has-vehicle="{{ $vehicle || old('vehicle_id') ? '1' : '0' }}">

        <div class="mb-stack-md text-center md:text-left">
            <h1 class="mb-2 text-headline-lg text-primary">Formulir Peminjaman Kendaraan</h1>
            <p class="text-body-md text-on-surface-variant">Silakan lengkapi detail berikut untuk melakukan reservasi kendaraan dinas.</p>
        </div>

        {{-- Stepper --}}
        <div class="mx-auto mb-stack-md max-w-3xl">
            {{-- Three equal columns, so every circle sits at the centre of its column. The line runs from the
                 centre of the first circle to the centre of the last one (1/6 inset on each side) and so ends
                 exactly at the circles instead of sticking out past them. --}}
            <div class="relative grid grid-cols-3">
                @foreach (['Detail Peminjam', 'Jadwal & Tujuan', 'Konfirmasi'] as $i => $label)
                    <div class="z-10 flex flex-col items-center gap-2 text-center" data-step-header="{{ $i + 1 }}">
                        <div data-step-circle class="flex h-10 w-10 items-center justify-center rounded-full bg-surface-container-high font-bold text-outline">{{ $i + 1 }}</div>
                        <span data-step-label class="text-label-md text-outline">{{ $label }}</span>
                    </div>
                @endforeach
                <div class="absolute left-[16.6667%] right-[16.6667%] top-5 -z-0 h-0.5 bg-outline-variant"><div class="h-full bg-primary transition-all duration-500" data-progress style="width:0%"></div></div>
            </div>
        </div>

        <div class="mx-auto max-w-3xl rounded-xl border border-outline-variant bg-surface-container-lowest p-stack-md md:p-stack-lg">
            <form method="POST" action="{{ route('booking.store') }}" class="space-y-stack-lg" data-wizard-form novalidate>
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ old('vehicle_id', $vehicle?->id) }}">

                {{-- Step 1: Validasi NIP --}}
                <div class="form-step space-y-gutter" data-step="1">
                    <div class="mb-2 flex justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#dcfce7]"><span class="material-symbols-outlined text-4xl text-[#15803d]">verified_user</span></div>
                    </div>
                    <div class="mx-auto grid max-w-md grid-cols-1 gap-gutter">
                        <div class="mb-2 text-center">
                            <h2 class="mb-2 text-headline-md text-on-background">Validasi NIP Pegawai</h2>
                            <p class="text-body-md text-on-surface-variant">Masukkan NIP Pegawai Anda untuk verifikasi hak peminjaman mobil dinas.</p>
                        </div>
                        <div class="flex flex-col gap-stack-sm">
                            <div data-nip-error @class(['mb-2 items-start gap-3 rounded-xl border border-error bg-error-container p-4 text-error', 'hidden' => ! $errors->has('nip'), 'flex' => $errors->has('nip')]) role="alert">
                                <span class="material-symbols-outlined mt-0.5">error</span>
                                <p class="text-body-sm font-medium" data-nip-error-text>{{ $errors->first('nip') }}</p>
                            </div>
                            <label for="nip" class="text-label-md font-bold text-on-surface">NIP (Nomor Induk Pegawai) 18 Digit *</label>
                            <input id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" autocomplete="off"
                                   value="{{ old('nip') }}" placeholder="Contoh: 198811042012021001"
                                   class="{{ $inputBase }} px-4 border-outline-variant">
                        </div>
                    </div>
                </div>

                {{-- Step 2: Jadwal & tujuan --}}
                <div class="form-step space-y-gutter" data-step="2" hidden>
                    <div data-step2-error @class(['items-start gap-3 rounded-xl border border-error bg-error-container p-4 text-error', 'hidden' => $stepErrors->isEmpty(), 'flex' => $stepErrors->isNotEmpty()]) role="alert">
                        <span class="material-symbols-outlined mt-0.5">error</span>
                        <div>
                            <p class="mb-1 text-body-sm font-bold">Lengkapi data berikut sebelum melanjutkan:</p>
                            <ul class="list-disc pl-5 text-body-sm" data-step2-error-list>
                                @foreach ($stepErrors as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-gutter md:grid-cols-2">
                        <div class="flex flex-col gap-stack-sm">
                            <label class="text-label-md text-on-surface">Kendaraan Dipilih *</label>
                            <input type="text" readonly data-vehicle-name
                                   value="{{ $vehicle ? $vehicle->name.' ('.$vehicle->plate.')' : (old('vehicle_id') ? \App\Models\Vehicle::find(old('vehicle_id'))?->name : 'Belum dipilih — pilih dari Katalog Mobil') }}"
                                   class="{{ $inputBase }} border-outline-variant bg-surface-container-low px-4">
                            @unless ($vehicle || old('vehicle_id'))
                                <a href="{{ route('katalog') }}" class="text-body-sm text-primary hover:underline">Buka Katalog Mobil →</a>
                            @endunless
                        </div>
                        <div></div>

                        <div class="flex flex-col gap-stack-sm">
                            <label for="departs_on" class="text-label-md text-on-surface">Tanggal Keberangkatan *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3 text-outline">calendar_month</span>
                                <input id="departs_on" name="departs_on" type="date" value="{{ old('departs_on') }}" min="{{ $earliest->toDateString() }}"
                                       @class([$inputBase, 'pl-10 pr-4', 'border-outline-variant' => ! $errors->has('departs_on'), 'border-error ring-2 ring-error/30' => $errors->has('departs_on')])>
                            </div>
                            <p class="text-body-sm text-on-surface-variant">Paling cepat <strong>{{ $earliestLabel }}</strong>. Hari H tidak dapat diajukan, akhir pekan dan hari libur tetap bisa dipilih sebagai tanggal pemakaian.</p>
                        </div>
                        <div class="flex flex-col gap-stack-sm">
                            <label for="returns_on" class="text-label-md text-on-surface">Tanggal Kembali *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3 text-outline">event_repeat</span>
                                {{-- min follows the departure date (kept in sync by booking-wizard.js); this is its initial value. --}}
                                <input id="returns_on" name="returns_on" type="date" value="{{ old('returns_on') }}" min="{{ old('departs_on') ?: $earliest->toDateString() }}"
                                       @class([$inputBase, 'pl-10 pr-4', 'border-outline-variant' => ! $errors->has('returns_on'), 'border-error ring-2 ring-error/30' => $errors->has('returns_on')])>
                            </div>
                        </div>
                        <div class="flex flex-col gap-stack-sm md:col-span-2">
                            <label for="destination" class="text-label-md text-on-surface">Lokasi Tujuan *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3 text-outline">location_on</span>
                                <input id="destination" name="destination" type="text" maxlength="255" value="{{ old('destination') }}" placeholder="Masukkan alamat atau nama lokasi tujuan"
                                       autocomplete="off" data-place-search="{{ route('places.search') }}"
                                       role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="destination-suggestions"
                                       @class([$inputBase, 'pl-10 pr-4', 'border-outline-variant' => ! $errors->has('destination'), 'border-error ring-2 ring-error/30' => $errors->has('destination')])>
                                <ul id="destination-suggestions" role="listbox" data-place-list
                                    class="absolute left-0 right-0 top-full z-30 mt-1 hidden overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-lg"></ul>
                            </div>
                            <p class="text-body-sm text-on-surface-variant">Ketik minimal 3 huruf untuk melihat saran tempat dari peta. Tempat yang tidak muncul tetap dapat ditulis bebas.</p>
                        </div>
                        <div class="flex flex-col gap-stack-sm md:col-span-2">
                            <label for="purpose" class="text-label-md text-on-surface">Keperluan *</label>
                            <textarea id="purpose" name="purpose" rows="3" maxlength="1000" placeholder="Jelaskan tujuan penggunaan kendaraan secara singkat..."
                                      @class(['w-full rounded-lg border p-4 transition-all focus:border-primary focus:ring-2 focus:ring-primary/20', 'border-outline-variant' => ! $errors->has('purpose'), 'border-error ring-2 ring-error/30' => $errors->has('purpose')])>{{ old('purpose') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Konfirmasi --}}
                <div class="form-step space-y-gutter" data-step="3" hidden>
                    <div class="rounded-lg border border-outline-variant bg-surface-container-low p-stack-md">
                        <h3 class="mb-4 flex items-center gap-2 text-headline-sm text-primary"><span class="material-symbols-outlined">info</span> Ringkasan Pesanan</h3>
                        <div class="divide-y divide-outline-variant">
                            @foreach (['employee' => 'Nama Pegawai', 'nip' => 'NIP', 'vehicle' => 'Kendaraan', 'schedule' => 'Jadwal', 'destination' => 'Tujuan', 'purpose' => 'Keperluan'] as $key => $label)
                                <div class="flex justify-between gap-4 py-2">
                                    <span class="font-medium text-on-surface-variant">{{ $label }}</span>
                                    <span class="text-right text-on-surface {{ $key === 'vehicle' ? 'font-semibold' : '' }}" data-summary="{{ $key }}">-</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-outline-variant pt-stack-md">
                    <button type="button" data-prev class="hidden h-12 items-center gap-2 rounded-lg border border-primary px-6 text-label-md text-primary transition-all hover:bg-primary/5"><span class="material-symbols-outlined">arrow_back</span> Kembali</button>
                    <div class="flex-grow"></div>
                    <button type="button" data-next class="flex h-12 items-center gap-2 rounded-lg bg-primary px-8 text-label-md text-white transition-all hover:bg-primary-container disabled:opacity-60">Lanjut <span class="material-symbols-outlined">arrow_forward</span></button>
                    <button type="submit" data-submit class="hidden h-12 items-center gap-2 rounded-lg bg-primary px-8 text-label-md text-white shadow-sm transition-all hover:bg-primary-container">Kirim Reservasi <span class="material-symbols-outlined">send</span></button>
                </div>
            </form>
        </div>

        <div class="mx-auto mt-stack-lg grid max-w-3xl grid-cols-1 gap-gutter md:grid-cols-2">
            <div class="flex gap-4 rounded-xl border border-secondary-container bg-secondary-container/30 p-4">
                <span class="material-symbols-outlined text-3xl text-primary">help_center</span>
                <div><h4 class="text-label-md">Butuh Bantuan?</h4><p class="text-body-sm text-on-surface-variant">Hubungi bagian administrasi di (0281) 123456 jika mengalami kendala sistem.</p></div>
            </div>
            <div class="flex gap-4 rounded-xl border border-primary-fixed bg-primary-fixed/30 p-4">
                <span class="material-symbols-outlined text-3xl text-primary">policy</span>
                <div><h4 class="text-label-md">Syarat &amp; Ketentuan</h4><p class="text-body-sm text-on-surface-variant">Peminjaman harus diajukan minimal 1 hari kerja sebelum keberangkatan.</p></div>
            </div>
        </div>
    </div>
</x-layouts.app>
