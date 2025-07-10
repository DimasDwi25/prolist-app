@props(['label', 'name', 'value' => old($name)])

<div class="mb-4">
    <label class="block text-gray-700 font-semibold mb-1">{{ $label }}</label>
    <input type="text" name="{{ $name }}" value="{{ $value }}" class="w-full border px-4 py-2 rounded">
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
