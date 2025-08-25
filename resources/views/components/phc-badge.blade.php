@props(['label', 'value'])

@php
    $classes = match($value) {
        'A'  => 'bg-green-50 text-green-700 border-green-200',
        'NA' => 'bg-gray-50 text-gray-500 border-gray-200',
        default => 'bg-blue-50 text-blue-700 border-blue-200',
    };
@endphp

<div class="space-y-1">
    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
    <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $classes }}">
        {{ $value ?? 'N/A' }}
    </span>
</div>
