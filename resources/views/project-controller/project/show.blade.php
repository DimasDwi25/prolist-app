@extends('project-controller.layouts.app')

@section('content')
    @php
        $role = Auth::user()->role->name ?? '';
    @endphp

    <div class="max-w-6xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow border border-gray-200">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold">View Project</h2>
                <a href="{{ route('project_controller.project.index') }}" class="text-sm text-gray-600 hover:underline">←
                    Back to Projects</a>
            </div>

            <div class="space-x-2">
                @php $hasPhc = $project->phc; @endphp

                @if ($hasPhc)
                    <a href="{{ route('project_controller.phc.show', $project->phc->id) }}"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition">
                        👁️ View PHC
                    </a>
                @else
                    <span class="inline-block bg-gray-300 text-gray-600 px-4 py-2 rounded text-sm cursor-not-allowed"
                        title="PHC belum tersedia">
                        🔒 View PHC
                    </span>
                @endif

                <a href="{{ route('projects.logs', $project->id) }}"
                    class="inline-flex items-center bg-gray-700 text-white px-3 py-1.5 rounded hover:bg-gray-800 text-sm">
                    📋 View Logs
                </a>

                {{-- Tombol Project Schedule --}}
                <a href="{{ route('projects.schedules.index', $project->id) }}"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm transition">
                    🗓️ Project Schedule
                </a>

                <a href="{{ route('projects.schedule.weekly-progress-all', [$project->id]) }}">
                    📈 Lihat S-Curve
                </a>



            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'info' }">
            <div class="border-b mb-4">
                <nav class="flex space-x-4 text-sm font-medium text-gray-600">
                    <button class="px-4 py-2 focus:outline-none transition"
                        :class="tab === 'info' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'hover:text-blue-500'"
                        @click="tab = 'info'">
                        📁 Informasi Proyek
                    </button>
                    <button class="px-4 py-2 focus:outline-none transition"
                        :class="tab === 'status' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'hover:text-blue-500'"
                        @click="tab = 'status'">
                        📊 Status Proyek
                    </button>
                </nav>
            </div>

            @php
                $display = fn($value) => $value ?: '—';
                $formatDate = fn($date) => $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : '—';
                $formatDecimal = fn($decimal) => is_numeric($decimal) ? number_format($decimal, 0, ',', '.') : '—';
            @endphp

            {{-- Info Project --}}
            <div x-show="tab === 'info'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <x-view.label label="Project Number" :value="$display($project->project_number)" />
                    <x-view.label label="Project Name" :value="$display($project->project_name)" />
                    <x-view.label label="Categorie Name" :value="$display($project->category->name)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <x-view.label label="Mandays Engineer" :value="$display($project->mandays_engineer)" />
                    <x-view.label label="Mandays Technician" :value="$display($project->mandays_technician)" />
                    <x-view.label label="Target Date" :value="$formatDate($project->target_dates)" />
                </div>
            </div>

            {{-- Status Project --}}
            <div x-show="tab === 'status'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
                    <x-view.label label="Status Proyek" :value="$display($project->statusProject->name)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-view.label label="Created At" :value="$formatDate($project->created_at)" />
                    <x-view.label label="Updated At" :value="$formatDate($project->updated_at)" />
                </div>
            </div>
        </div>

        {{-- Project Logs --}}
        <div class="mt-8">
            <h3 class="text-xl font-bold text-gray-700 mb-4">📜 Project Logs</h3>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-4">

                {{-- Desktop (DataTable) --}}
                <div class="">
                    @livewire('log.log-table', ['projectId' => $project->id])
                </div>
            </div>
        </div>
    </div>

@endsection