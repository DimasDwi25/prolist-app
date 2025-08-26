@php
    $roleLayouts = [
        'project controller'     => 'project-controller.layouts.app',
        'engineer'     => 'engineer.layouts.app',
        'engineering_manager'         => 'project-manager.layouts.app',
        'engineering_director'  => 'engineering_director.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Project Schedules</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Project: <span class="font-semibold">{{ $project->project_number ?? $project->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Back Button --}}
                <a href="{{ route('engineer.project.show', $project) }}"
                    class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                {{-- Add Schedule Button --}}
                <a href="{{ route('projects.schedules.create', $project->pn_number) }}"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Schedule
                </a>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
            <div class="mb-4 flex items-center p-3 bg-green-50 border border-green-300 text-green-700 rounded-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="border-b p-3 text-left">Schedule Name</th>
                        <th class="border-b p-3 text-left">Created At</th>
                        <th class="border-b p-3 text-center w-56">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-3 text-gray-800 font-medium">{{ $schedule->name }}</td>
                            <td class="p-3 text-gray-500">
                                {{ $schedule->created_at->format('d M Y') }}
                            </td>
                            <td class="p-3 text-center space-x-2">
                                {{-- Edit --}}
                                <a href="{{ route('projects.schedules.edit', [$project->pn_number, $schedule->id]) }}"
                                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded-lg shadow transition duration-150">
                                    Edit
                                </a>
                                {{-- Manage Tasks --}}
                                <a href="{{ route('projects.schedule-tasks.index', [$project->pn_number, $schedule->id]) }}"
                                    class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-lg shadow transition duration-150">
                                    Manage Tasks
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('projects.schedules.destroy', [$project->pn_number, $schedule->id]) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure to delete this schedule?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-lg shadow transition duration-150">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500">No schedules found for this project.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    </div>
@endsection