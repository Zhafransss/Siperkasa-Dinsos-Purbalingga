@props(['title', 'subtitle' => null])

<div class="mb-stack-lg flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-headline-lg-mobile text-navy sm:text-headline-lg">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-body-md text-on-surface-variant">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
    @endisset
</div>
