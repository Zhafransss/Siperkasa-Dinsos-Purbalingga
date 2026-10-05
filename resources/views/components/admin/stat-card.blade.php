@props(['label', 'value', 'icon', 'tone' => 'navy', 'hint' => null, 'hintTone' => 'muted'])

@php
    $tile = [
        'navy' => 'bg-navy/10 text-navy',
        'green' => 'bg-success/10 text-success',
        'amber' => 'bg-warning/10 text-warning',
        'blue' => 'bg-primary/10 text-primary',
    ][$tone] ?? 'bg-navy/10 text-navy';
    $hintClass = ['muted' => 'text-on-surface-variant', 'green' => 'text-success', 'amber' => 'text-warning'][$hintTone] ?? 'text-on-surface-variant';
@endphp

<div class="flex items-center gap-4 rounded-xl border border-outline-variant bg-white/70 p-5 shadow-sm backdrop-blur">
    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $tile }}">
        <span class="material-symbols-outlined text-[26px]">{{ $icon }}</span>
    </span>
    <div class="min-w-0">
        <p class="text-label-sm uppercase tracking-wider text-outline">{{ $label }}</p>
        <p class="text-headline-md text-navy">{{ $value }}</p>
        @if ($hint)
            <p class="text-label-sm {{ $hintClass }}">{{ $hint }}</p>
        @endif
    </div>
</div>
