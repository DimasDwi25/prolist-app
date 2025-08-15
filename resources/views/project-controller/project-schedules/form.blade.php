@extends('project-controller.layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $schedule->exists ? 'Edit Schedule' : 'Add New Schedule' }}
            </h2>
            <a href="{{ route('projects.schedules.index', $project->pn_number) }}"
                class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ $schedule->exists
        ? route('projects.schedules.update', [$project->pn_number, $schedule->id])
        : route('projects.schedules.store', $project->pn_number) }}" class="space-y-6">
            @csrf
            @if($schedule->exists)
                @method('PUT')
            @endif

            {{-- Schedule Name --}}
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">
                    Schedule Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $schedule->name) }}"
                    class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Enter schedule name...">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3">
                <button type="submit"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow transition duration-200">
                    @if($schedule->exists)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Schedule
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Save Schedule
                    @endif
                </button>
            </div>
        </form>
    </div>
@endsection