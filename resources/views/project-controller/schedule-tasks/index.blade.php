@php
    $roleLayouts = [
        'project controller'     => 'project-controller.layouts.app',
        'engineer'     => 'engineer.layouts.app',
        'engineering_manager'         => 'project-manager.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Tasks</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Project: <span class="font-semibold">{{ $project->project_number }}</span> <br>
                    Schedule: <span class="font-semibold">{{ $schedule->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Back Button --}}
                <a href="{{ route('projects.schedules.index', $project->pn_number) }}"
                    class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                {{-- Add Task Button --}}
                <a href="{{ route('projects.schedule-tasks.create', [$project->pn_number, $schedule->id]) }}"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Task
                </a>
            </div>
        </div>

        {{-- Success Notification --}}
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
                        <th class="border-b p-3 text-left">Task</th>
                        <th class="border-b p-3 text-center">Qty</th>
                        <th class="border-b p-3 text-center">Unit</th>
                        <th class="border-b p-3 text-center">Bobot (%)</th>
                        <th class="border-b p-3 text-center">Plan Start</th>
                        <th class="border-b p-3 text-center">Plan Finish</th>
                        <th class="border-b p-3 text-center">Actual Start</th>
                        <th class="border-b p-3 text-center">Actual Finish</th>
                        <th class="border-b p-3 text-center w-48">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-3 text-gray-800">{{ $task->task->task_name ?? 'N/A' }}</td>
                            <td class="p-3 text-center text-gray-600">{{ $task->quantity }}</td>
                            <td class="p-3 text-center text-gray-600">{{ $task->unit ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold text-gray-700">{{ number_format($task->bobot, 2) }}%</td>

                            {{-- Plan Start & Finish --}}
                            <td class="p-3 text-center text-gray-500">
                                {{ $task->plan_start ? \Carbon\Carbon::parse($task->plan_start)->format('d M Y') : '-' }}
                            </td>
                            <td class="p-3 text-center text-gray-500">
                                {{ $task->plan_finish ? \Carbon\Carbon::parse($task->plan_finish)->format('d M Y') : '-' }}
                            </td>

                            {{-- Actual Start & Finish --}}
                            <td class="p-3 text-center text-gray-500">
                                {{ $task->actual_start ? \Carbon\Carbon::parse($task->actual_start)->format('d M Y') : '-' }}
                            </td>
                            <td class="p-3 text-center text-gray-500">
                                {{ $task->actual_finish ? \Carbon\Carbon::parse($task->actual_finish)->format('d M Y') : '-' }}
                            </td>

                            {{-- Actions --}}
                            <td class="p-3 text-center space-x-2">
                                <a href="{{ route('projects.schedule-tasks.edit', [$project->pn_number, $schedule->id, $task->id]) }}"
                                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded-lg shadow transition duration-150">
                                    Edit
                                </a>
                                <a href="{{ route('projects.schedule-tasks.weekly-progress', [$project->pn_number, $schedule->id, $task->id]) }}"
                                    class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-lg shadow transition duration-150">
                                    Weekly Progress
                                </a>
                                <form
                                    action="{{ route('projects.schedule-tasks.destroy', [$project->pn_number, $schedule->id, $task->id]) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this task?');">
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
                            <td colspan="9" class="p-4 text-center text-gray-500">No tasks found for this schedule.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>
@endsection