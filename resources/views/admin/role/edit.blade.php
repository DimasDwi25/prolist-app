@extends('admin.layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                <span>✏️</span>
                <span>Edit Role</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Update role information below.</p>
        </div>

        <form action="{{ route('role.update', $role) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Role Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $role->name) }}"
                       placeholder="e.g. Engineer"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type Role --}}
            <div>
                <label for="type_role" class="block text-sm font-medium text-gray-700 mb-1">Type Role</label>
                <select name="type_role" id="type_role"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('type_role') border-red-500 @enderror">
                    <option value="">-- Select Type Role --</option>
                    <option value="1" {{ old('type_role', $role->type_role) == '1' ? 'selected' : '' }}>Type 1</option>
                    <option value="2" {{ old('type_role', $role->type_role) == '2' ? 'selected' : '' }}>Type 2</option>
                </select>
                @error('type_role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.role') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Back</span>
                </a>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-md shadow">
                    Update Role
                </button>
            </div>
        </form>
    </div>
@endsection
