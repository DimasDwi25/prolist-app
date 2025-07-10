{{-- resources/views/components/input/text.blade.php --}}
@props([
    'name',
    'label',
    'value' => '',
    'disabled' => false
])

<div>
    <label for="{{ $name }}" class="text-sm text-gray-600">{{ $label }}</label>
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'w-full border rounded px-3 py-2']) }}
    >
</div>
