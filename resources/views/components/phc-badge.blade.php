@props(['label', 'value'])

<div>
    <p class="text-sm text-gray-500">{{ $label }}</p>
    <span class="inline-block px-3 py-1 text-sm rounded 
        {{ $value ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
        {{ $value ?? '❌ N/A' }}
    </span>
</div>
