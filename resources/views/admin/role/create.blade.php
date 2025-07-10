@extends('admin.layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">➕ Add Role</h1>
            <a href="{{ route('admin.department') }}" class="text-sm text-blue-600 hover:underline">← Back</a>
        </div>

        <form action="{{ route('role.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">Role Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    placeholder="e.g marketing">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="type_role" class="block text-gray-700 font-medium mb-2">Type Role</label>
                <select name="type_role" id="type_role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">-- Select Type Role --</option>
                    <option value="1" {{ old('type_role') == '1' ? 'selected' : '' }}>Type 1</option>
                    <option value="2" {{ old('type_role') == '2' ? 'selected' : '' }}>Type 2</option>
                </select>
                @error('type_role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.department') }}"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 shadow-sm text-sm">
                    Save Role
                </button>
            </div>
        </form>
    </div>
@endsection