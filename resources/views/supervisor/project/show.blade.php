@extends('supervisor.layouts.app')

@section('content')
    @php
        $role = Auth::user()->role->name ?? '';
    @endphp

    <div class="max-w-6xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow border border-gray-200">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📁 View Project</h2>
                <a href="{{ route('supervisor.project') }}"
                    class="text-sm text-gray-500 hover:text-blue-600 hover:underline">
                    ← Back to Projects
                </a>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('project.edit', $project) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm shadow transition">
                    ✏️ Edit Project
                </a>

                @if ($project->phc)
                    <a href="{{ route('phc.edit', $project->phc->id) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm shadow transition">
                        ✏️ Edit PHC
                    </a>

                    <a href="{{ route('phc.show', $project->phc->id) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm shadow transition">
                        👁️ View PHC
                    </a>
                @else
                    <a href="{{ route('phc', $project->id) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm shadow transition">
                        ➕ Create PHC
                    </a>
                @endif

                <a href="{{ route('supervisor.projects.logs', $project->id) }}"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm shadow transition">
                    📋 View Logs
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
                        :class="tab === 'quotation' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'hover:text-blue-500'"
                        @click="tab = 'quotation'">
                        📄 Informasi Quotation
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

            {{-- Info Quotation --}}
            <div x-show="tab === 'quotation'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <x-view.label label="No. Quotation" :value="$display($project->quotation->no_quotation)" />
                    <x-view.label label="Client" :value="$display($project->quotation->client->name)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <x-view.label label="PO Value" :value="$formatDecimal($project->quotation->po_value)" />
                    <x-view.label label="PO Number" :value="$display($project->quotation->po_number)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <x-view.label label="PO Date" :value="$formatDate($project->quotation->po_date)" />
                </div>
            </div>

            {{-- Info Status --}}
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
                <div class="hidden md:block">
                    @livewire('log.log-table', ['projectId' => $project->id])
                </div>
            </div>
        </div>
@endsection