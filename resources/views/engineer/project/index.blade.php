@extends('engineer.layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📁 Projects</h1>
        <a href="{{ route('project.create') }}"
            class="inline-flex items-center bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
            + New Project
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 font-medium shadow">
            {{ session('success') }}
        </div>
    @endif

    @if ($projects->isEmpty())
        <div class="bg-yellow-50 text-yellow-800 border-l-4 border-yellow-500 p-4 rounded-md shadow-sm">
            No project found. Start by creating one.
        </div>
    @else
        <div class="bg-white shadow-sm rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Project Number</th>
                        <th class="px-6 py-3 text-left font-semibold">Name</th>
                        <th class="px-6 py-3 text-left font-semibold">Category</th>
                        <th class="px-6 py-3 text-left font-semibold">Quotation</th>
                        <th class="px-6 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($projects as $project)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $project->project_number }}</td>
                            <td class="px-6 py-4">{{ $project->project_name }}</td>
                            <td class="px-6 py-4">{{ $project->category->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $project->quotation->no_quotation ?? '-' }}</td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="{{ route('project.show', $project) }}"
                                    class="inline-flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium">
                                    👁️ View
                                </a>
                                <a href="{{ route('project.edit', $project) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    ✏️ Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection