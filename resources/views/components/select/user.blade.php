@props([
    'name',
    'label' => null,
    'users' => [],
    'role' => null, 
    'selected' => null,
    'disabled' => false,
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <select id="{{ $name }}" name="{{ $name }}"
        @if($disabled) disabled @endif
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
        <option value="">-- Select {{ $label ?? 'User' }} --</option>
        @foreach($users as $user)
            @if(!$role || (isset($user->role) && strtolower($user->role) === strtolower($role)))
                <option value="{{ $user->id }}" @selected(old($name, $selected) == $user->id)>
                    {{ $user->name }}
                </option>
            @endif
        @endforeach
    </select>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
