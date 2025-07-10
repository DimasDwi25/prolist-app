@extends('marketing.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ isset($category) ? '✏️ Edit' : '➕ Create' }} Project Category
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ isset($category) ? 'Update the details below to modify this category.' : 'Fill out the form below to create a new project category.' }}
                </p>
            </div>

            <form method="POST"
                action="{{ isset($category) ? route('category.update', $category->id) : route('category.store') }}"
                class="space-y-6">
                @csrf
                @if(isset($category))
                    @method('PUT')
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="e.g. Infrastructure Projects" required>
                    @error('name')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Enter a brief description..."
                        required>{{ old('description', $category->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('marketing.category') }}"
                        class="text-gray-600 hover:underline text-sm flex items-center">
                        ← Back to List
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-lg shadow-md transition">
                        {{ isset($category) ? '💾 Update' : '✅ Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection