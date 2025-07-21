<div class="space-y-1">
    <label class="block text-xs text-gray-500 font-semibold uppercase">
        {{ $filter->label }}
    </label>
    <input
        type="text"
        data-flatpickr
        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-200"
        wire:input.debounce.500ms="$set('filters.{{ $filter->key }}', $event.detail)"
        value="{{ $this->getFilter($filter->key) }}"
    />
</div>
