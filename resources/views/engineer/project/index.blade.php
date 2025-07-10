@extends('engineer.layouts.app')

@section('content')
   

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
                                <a href="{{ route('engineer.project.show', $project) }}" class="text-gray-700 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
