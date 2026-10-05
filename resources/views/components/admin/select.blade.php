@props(['name', 'label', 'options', 'value' => null, 'required' => false, 'placeholder' => null, 'span' => null])

{{-- Labelled <select>. $options is [value => label]. --}}
@php
    $id = 'f-'.str_replace(['[', ']', '.'], '-', $name);
    $error = $errors->first($name);
    $current = old($name, $value instanceof \BackedEnum ? $value->value : $value);
@endphp

<div @class(['flex flex-col gap-2', $span])>
    <label for="{{ $id }}" class="text-label-md text-on-surface-variant">{{ $label }}@if ($required) <span class="text-error">*</span>@endif</label>
    <select id="{{ $id }}" name="{{ $name }}"
            @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            @class([
                'h-12 w-full rounded-lg border bg-white px-4 text-body-sm transition-all focus:border-navy focus:ring-2 focus:ring-navy/20',
                'border-outline-variant' => ! $error,
                'border-error ring-2 ring-error/30' => $error,
            ])>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($error)
        <p id="{{ $id }}-error" class="text-body-sm text-error">{{ $error }}</p>
    @endif
</div>
