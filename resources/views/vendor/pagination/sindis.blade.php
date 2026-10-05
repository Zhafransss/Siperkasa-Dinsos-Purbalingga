{{-- Numbered pager from the Figma Katalog frame. Rendered even for a single page, like the design. --}}
@php
    $btn = 'flex h-10 w-10 items-center justify-center rounded-lg border text-label-md transition-colors';
@endphp

<nav class="mt-stack-lg flex items-center justify-center gap-2" role="navigation" aria-label="Navigasi halaman">
    @if ($paginator->onFirstPage())
        <span class="{{ $btn }} cursor-not-allowed border-outline-variant text-on-surface-variant opacity-40" aria-hidden="true"><span class="material-symbols-outlined">chevron_left</span></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $btn }} border-outline-variant text-on-surface-variant hover:bg-surface-container-low" aria-label="Sebelumnya"><span class="material-symbols-outlined">chevron_left</span></a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-1 text-on-surface-variant">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="{{ $btn }} border-primary bg-primary text-white" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="{{ $btn }} border-outline-variant text-on-surface hover:bg-surface-container-low" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $btn }} border-outline-variant text-on-surface-variant hover:bg-surface-container-low" aria-label="Berikutnya"><span class="material-symbols-outlined">chevron_right</span></a>
    @else
        <span class="{{ $btn }} cursor-not-allowed border-outline-variant text-on-surface-variant opacity-40" aria-hidden="true"><span class="material-symbols-outlined">chevron_right</span></span>
    @endif
</nav>
