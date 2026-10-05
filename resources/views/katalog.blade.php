<x-layouts.app title="Katalog Mobil">
    <div class="mx-auto max-w-container-max px-margin-mobile pb-stack-lg pt-stack-lg md:px-margin-desktop">
        <div class="mb-stack-lg">
            <h1 class="mb-2 text-headline-lg text-on-background">Katalog Kendaraan Dinas</h1>
            <p class="text-body-md text-on-surface-variant">Pilih kendaraan yang tersedia untuk mendukung operasional layanan kesehatan di Kabupaten Purbalingga.</p>
        </div>

        <div class="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($vehicles as $vehicle)
                <x-vehicle-card :vehicle="$vehicle" />
            @empty
                <p class="col-span-full py-stack-lg text-center text-on-surface-variant">Belum ada kendaraan terdaftar.</p>
            @endforelse
        </div>

        {{ $vehicles->links() }}
    </div>
</x-layouts.app>
