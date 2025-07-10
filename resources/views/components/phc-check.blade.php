@props(['label', 'value'])

<div>
    <p class="text-sm text-gray-500">{{ $label }}</p>
    <p class="{{ $value ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
        {{ $value ? '✅ A (Applicable)' : '❌ N/A' }}
    </p>
</div>
