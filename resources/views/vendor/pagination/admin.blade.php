{{--
    Admin table/grid footer: "Menampilkan X-Y dari N <unit>" on the left, numbered pager on the right.
    Pass the noun with ->links('vendor.pagination.admin', ['unit' => 'pegawai']). Rendered only when there is more than one page.
--}}
@php
    $btn = 'flex h-8 min-w-8 items-center justify-center rounded-lg border px-2 text-label-md transition-colors';
    $unit = $unit ?? 'data';
@endphp

@if ($paginator->hasPages())
    <nav class="mt-stack-md flex flex-col items-center justify-between gap-3 rounded-xl border border-outline-variant bg-surface-container-low px-5 py-4 sm:flex-row" role="navigation" aria-label="Navigasi halaman">
        <p class="text-body-sm text-on-surface-variant">Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} {{ $unit }}</p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="{{ $btn }} cursor-not-allowed border-outline-variant text-outline opacity-50" aria-hidden="true"><span class="material-symbols-outlined text-[18px]">chevron_left</span></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" class="{{ $btn }} border-outline-variant bg-white text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]">chevron_left</span></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-on-surface-variant">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="{{ $btn }} border-navy bg-navy text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="{{ $btn }} border-outline-variant bg-white text-on-surface hover:bg-surface-container-low">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya" class="{{ $btn }} border-outline-variant bg-white text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]">chevron_right</span></a>
            @else
                <span class="{{ $btn }} cursor-not-allowed border-outline-variant text-outline opacity-50" aria-hidden="true"><span class="material-symbols-outlined text-[18px]">chevron_right</span></span>
            @endif
        </div>
    </nav>
@endif
