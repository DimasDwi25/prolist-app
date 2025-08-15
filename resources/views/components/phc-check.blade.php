@props(['label', 'value'])

<div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-all duration-200">
    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>

    @if($value)
        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
            A
        </span>
    @else
        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-50 text-gray-500 border border-gray-200">
            N/A
        </span>
    @endif
</div>
