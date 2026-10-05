@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'suffix' => null, 'placeholder' => null, 'required' => false, 'readonly' => false, 'inputmode' => null, 'maxlength' => null, 'min' => null, 'max' => null, 'list' => null, 'span' => null])

{{-- Labelled text input with error + hint. Old input wins over $value after a failed submit. --}}
@php
    $id = $attributes->get('id', 'f-'.str_replace(['[', ']', '.'], '-', $name));
    $error = $errors->first(str_replace(['[', ']'], ['.', ''], $name));
@endphp

<div @class(['flex flex-col gap-2', $span])>
    <label for="{{ $id }}" class="text-label-md text-on-surface-variant">{{ $label }}@if ($required) <span class="text-error">*</span>@endif</label>
    <div class="relative">
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old(str_replace(['[', ']'], ['.', ''], $name), $value) }}"
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               @if ($inputmode) inputmode="{{ $inputmode }}" @endif
               @if ($maxlength) maxlength="{{ $maxlength }}" @endif
               @if ($min !== null) min="{{ $min }}" @endif
               @if ($max !== null) max="{{ $max }}" @endif
               @if ($list) list="{{ $list }}" @endif
               @if ($readonly) readonly @endif
               @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
               @class([
                   'h-12 w-full rounded-lg border bg-white px-4 text-body-sm transition-all focus:border-navy focus:ring-2 focus:ring-navy/20',
                   'border-outline-variant' => ! $error,
                   'border-error ring-2 ring-error/30' => $error,
                   'bg-surface-container-low' => $readonly,
                   'pr-14' => $suffix,
               ])>
        @if ($suffix)
            <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-label-sm text-on-surface-variant">{{ $suffix }}</span>
        @endif
    </div>
    @if ($error)
        <p id="{{ $id }}-error" class="text-body-sm text-error">{{ $error }}</p>
    @elseif ($hint)
        <p class="text-label-sm text-on-surface-variant">{{ $hint }}</p>
    @endif
</div>
