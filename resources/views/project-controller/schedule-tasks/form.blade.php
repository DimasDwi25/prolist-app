@extends('project-controller.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $task->exists ? 'Edit Task' : 'Add Task' }}
            </h2>
            <a href="{{ route('projects.schedule-tasks.index', [$project->id, $schedule->id]) }}"
                class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ $task->exists
        ? route('projects.schedule-tasks.update', [$project->id, $schedule->id, $task->id])
        : route('projects.schedule-tasks.store', [$project->id, $schedule->id]) }}" class="space-y-6">
            @csrf
            @if($task->exists)
                @method('PUT')
            @endif

            {{-- Select Task --}}
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Select Task (from Master) <span
                        class="text-red-500">*</span></label>
                <select name="task_id"
                    class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    <option value="">-- Choose Task --</option>
                    @foreach($taskMasters as $master)
                        <option value="{{ $master->id }}" {{ old('task_id', $task->task_id) == $master->id ? 'selected' : '' }}>
                            {{ $master->task_name }}
                        </option>
                    @endforeach
                </select>
                @error('task_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Quantity & Unit --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $task->quantity ?? 1) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $task->unit) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                </div>
            </div>

            {{-- Bobot & Order --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Bobot (%)</label>
                    <input type="number" step="0.01" name="bobot" value="{{ old('bobot', $task->bobot ?? 0) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    @error('bobot') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Order</label>
                    <input type="number" name="order" value="{{ old('order', $task->order ?? 0) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                </div>
            </div>

            {{-- Plan Dates --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Plan Start</label>
                    <input type="date" name="plan_start" value="{{ old('plan_start', $task->plan_start) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    @error('plan_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Plan Finish</label>
                    <input type="date" name="plan_finish" value="{{ old('plan_finish', $task->plan_finish) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    @error('plan_finish') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Actual Dates --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Actual Start</label>
                    <input type="date" name="actual_start" value="{{ old('actual_start', $task->actual_start) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Actual Finish</label>
                    <input type="date" name="actual_finish" value="{{ old('actual_finish', $task->actual_finish) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="submit"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow transition duration-200">
                    @if($task->exists)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Task
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Save Task
                    @endif
                </button>
            </div>
        </form>
    </div>
@endsection