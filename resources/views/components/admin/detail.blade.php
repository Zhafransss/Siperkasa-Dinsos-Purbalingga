@props(['label', 'value'])

{{-- Read-only label/value pair for detail pages. --}}
<div>
    <p class="text-label-sm text-on-surface-variant">{{ $label }}</p>
    <p class="mt-1 text-body-md font-medium text-on-background">{{ $value }}</p>
</div>
