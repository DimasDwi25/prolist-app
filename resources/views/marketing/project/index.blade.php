@extends('marketing.layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Projects</h1>
        <a href="{{ route('project.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ New Project</a>
    </div>

    @if (session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    @if ($projects->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p>No project found. Start by creating one.</p>
        </div>
    @else
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Project Number</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Category</th>
                        <th class="px-4 py-2 text-left">Quotation</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $project->project_number }}</td>
                            <td class="px-4 py-2">{{ $project->project_name }}</td>
                            <td class="px-4 py-2">{{ $project->category->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $project->quotation->no_quotation ?? '-' }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('project.show', $project) }}" class="text-gray-700 hover:underline">View</a>
                                <a href="{{ route('project.edit', $project) }}" class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
