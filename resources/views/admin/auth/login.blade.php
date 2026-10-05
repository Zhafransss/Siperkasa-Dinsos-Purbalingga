<x-admin.guest title="Masuk">
    <div class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_50%_40%,#d7e2ff55_0%,transparent_60%)] px-4 py-12">
        <div class="w-full max-w-md">
            <div class="rounded-xl border border-outline-variant bg-white p-8 shadow-sm sm:p-10">
                <div class="mb-8 text-center">
                    <img class="mx-auto mb-5 h-20 w-20 object-contain drop-shadow" alt="Logo Kabupaten Purbalingga" src="{{ asset('images/logo-dinkes.png') }}">
                    <h1 class="mb-2 text-headline-md text-navy">Masuk Admin</h1>
                    <p class="text-body-sm text-on-surface-variant">Silakan masukkan NIP dan kata sandi Anda untuk mengakses panel {{ config('app.name') }}.</p>
                </div>

                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-6" novalidate>
                    @csrf

                    @if ($errors->any())
                        <div class="flex items-start gap-3 rounded-xl border border-error bg-error-container p-4 text-error" role="alert">
                            <span class="material-symbols-outlined mt-0.5">error</span>
                            <p class="text-body-sm font-medium">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label for="nip" class="text-label-md text-on-surface-variant">NIP (ID Administrator)</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-outline">badge</span>
                            <input id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" autocomplete="username" autofocus
                                   value="{{ old('nip') }}" placeholder="Masukkan NIP 18 digit"
                                   class="h-12 w-full rounded-lg border border-outline-variant bg-surface pl-11 pr-4 text-body-sm transition-all focus:border-navy focus:ring-2 focus:ring-navy/20">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-label-md text-on-surface-variant">Kata Sandi</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••"
                                   class="h-12 w-full rounded-lg border border-outline-variant bg-surface pl-11 pr-12 text-body-sm transition-all focus:border-navy focus:ring-2 focus:ring-navy/20">
                            <button type="button" data-toggle-password="password" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-navy">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-navy text-label-md text-white shadow-[0_4px_6px_-4px_rgba(0,42,93,.2),0_10px_15px_-3px_rgba(0,42,93,.2)] transition-all hover:brightness-110">
                        Masuk ke Beranda <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>

                <div class="mt-8 flex items-start gap-3 border-t border-outline-variant pt-6">
                    <span class="material-symbols-outlined text-[20px] text-success">verified_user</span>
                    <p class="text-label-sm text-on-surface-variant">Halaman ini khusus administrator Dinas Kesehatan PPKB Purbalingga. Jangan bagikan kata sandi Anda. Lupa kata sandi? Minta administrator lain mengaturnya ulang.</p>
                </div>
            </div>
            <p class="mt-6 text-center text-label-sm text-outline"><a href="{{ route('home') }}" class="hover:underline">← Kembali ke halaman peminjaman</a></p>
        </div>
    </div>
</x-admin.guest>
