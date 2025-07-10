{{-- resources/views/components/input/date.blade.php --}}
@props(['name', 'label', 'value' => ''])

<div>
    <label for="{{ $name }}" class="text-sm text-gray-600">{{ $label }}</label>
    <input type="date"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ $value }}"
           {{ $attributes->merge(['class' => 'w-full border rounded px-3 py-2']) }}>
</div>
