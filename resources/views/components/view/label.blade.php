{{-- resources/views/components/view/label.blade.php --}}
@props(['label', 'value'])

<div>
    <label class="block text-sm text-gray-500 mb-1">{{ $label }}</label>
    <div class="text-gray-800 font-medium bg-gray-50 border px-3 py-2 rounded">{{ $value }}</div>
</div>
