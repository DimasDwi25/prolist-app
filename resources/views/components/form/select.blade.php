@props([
    'name',
    'label',
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'selected' => null,
    'required' => false,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} {{ $required ? '*' : '' }}
    </label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2']) }}
    >
        <option value="">-- Pilih {{ $label }} --</option>
        @foreach($options as $option)
            <option value="{{ $option[$optionValue] }}"
                @selected(old($name, $selected) == $option[$optionValue])>
                {{ $option[$optionLabel] }}
            </option>
        @endforeach
    </select>
</div>
