@props(['name', 'label', 'options', 'value' => null, 'required' => false, 'placeholder' => 'Pilih', 'span' => null])

{{-- Labelled dropdown that always opens below the field (a native <select> lets the browser pick the direction). $options is [value => label]. --}}
@php
    $id = 'f-'.str_replace(['[', ']', '.'], '-', $name);
    $error = $errors->first($name);
    $current = (string) old($name, $value instanceof \BackedEnum ? $value->value : $value);
    $currentLabel = $options[$current] ?? null;
@endphp

<div @class(['flex flex-col gap-2', $span]) data-dropdown>
    <label for="{{ $id }}" class="text-label-md text-on-surface-variant">{{ $label }}@if ($required) <span class="text-error">*</span>@endif</label>
    <div class="relative">
        <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-dropdown-value>
        <button type="button" id="{{ $id }}" data-dropdown-button aria-haspopup="listbox" aria-expanded="false"
                @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
                @class([
                    'flex h-12 w-full items-center justify-between gap-3 rounded-lg border bg-white px-4 text-left text-body-sm transition-all focus:border-navy focus:outline-none focus:ring-2 focus:ring-navy/20',
                    'border-outline-variant' => ! $error,
                    'border-error ring-2 ring-error/30' => $error,
                ])>
            <span data-dropdown-label @class(['truncate', 'text-outline' => $currentLabel === null])>{{ $currentLabel ?? $placeholder }}</span>
            <span class="material-symbols-outlined shrink-0 text-[20px] text-on-surface-variant">expand_more</span>
        </button>
        <ul role="listbox" tabindex="-1" data-dropdown-list hidden
            class="absolute left-0 right-0 top-full z-30 mt-1 max-h-64 overflow-y-auto rounded-lg border border-outline-variant bg-white py-1 shadow-lg">
            @foreach ($options as $optionValue => $optionLabel)
                <li role="option" tabindex="-1" data-value="{{ $optionValue }}" aria-selected="{{ (string) $optionValue === $current ? 'true' : 'false' }}"
                    class="cursor-pointer px-4 py-2.5 text-body-sm text-on-background hover:bg-surface-container-low focus:bg-surface-container-low focus:outline-none aria-selected:bg-navy/10 aria-selected:font-semibold aria-selected:text-navy">{{ $optionLabel }}</li>
            @endforeach
        </ul>
    </div>
    @if ($error)
        <p id="{{ $id }}-error" class="text-body-sm text-error">{{ $error }}</p>
    @endif
</div>
