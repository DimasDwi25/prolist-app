@extends('marketing.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Project Categories</h1>
    <a href="{{ route('category.create') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        + Add Category
    </a>
</div>

@if ($categories->isEmpty())
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
        <p>No project categories found. Start by creating one.</p>
    </div>
@else
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y">
                @foreach ($categories as $category)
                    <tr>
                        <td class="px-6 py-4">{{ $category->name }}</td>
                        <td class="px-6 py-4">{{ Str::limit($category->description, 80) }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('category.edit', $category->id) }}"
                                class="text-blue-600 hover:underline">Edit</a>
                            @can('delete', $category)
                            <form action="{{ route('category.destroy', $category->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
