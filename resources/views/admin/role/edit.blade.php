@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
            <span>✏️</span>
            <span>Edit Role</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">Update department information below.</p>
    </div>

    <form action="{{ route('role.update', $role) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-gray-700 font-medium mb-2">Role Name</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $role->name) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                placeholder="e.g. Finance"
            >
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.role') }}"
               class="text-gray-600 hover:underline text-sm flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Back</span>
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg shadow transition duration-200"
            >
                Update Role
            </button>
        </div>
    </form>
</div>
@endsection
