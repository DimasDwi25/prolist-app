@extends('engineer.layouts.app')

@section('content')
    @php
        $role = Auth::user()->role->name ?? '';
    @endphp
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold">View Project</h2>
                <a href="{{ route('supervisor.project') }}" class="text-sm text-gray-600 hover:underline">← Back to
                    Projects</a>
            </div>

            <div class="space-x-2">
                {{-- Tombol Edit Project --}}
                <a href="{{ route('project.edit', $project) }}"
                    class="inline-flex items-center bg-yellow-500 text-white px-3 py-1.5 rounded hover:bg-yellow-600 text-sm">
                    ✏️ Edit Project
                </a>

                @if ($project->phc)
                    <a href="{{ route('phc.edit', $project->phc->id) }}"
                        class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                        ✏️ Edit PHC
                    </a>

                    <a href="{{ route('phc.show', $project->phc->id) }}"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        👁️ View PHC
                    </a>
                @else
                    <a href="{{ route('phc', $project->pn_number) }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        ➕ Create PHC
                    </a>
                @endif


                {{-- Tombol View Log --}}
                <a href="{{ route('projects.logs', $project->pn_number) }}"
                    class="inline-flex items-center bg-gray-700 text-white px-3 py-1.5 rounded hover:bg-gray-800 text-sm">
                    📋 View Logs
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'info' }">
            <div class="border-b mb-4">
                <nav class="flex space-x-4">
                    <button class="px-4 py-2 font-medium"
                        :class="{ 'border-b-2 border-blue-600 text-blue-600': tab === 'info' }" @click="tab = 'info'">
                        📁 Informasi Proyek
                    </button>

                    <button class="px-4 py-2 font-medium" :class="{
                'border-b-2 border-blue-600 text-blue-600': tab === 'quotation'
            }" @click="tab = 'quotation'">📄 Informasi Quotation</button>
                    <button class="px-4 py-2 font-medium" :class="{
            'border-b-2 border-blue-600 text-blue-600': tab === 'status'
        }" @click="tab = 'status'">📊 Status Proyek</button>
                </nav>
            </div>
            @php
                $display = fn($value) => $value ?: '—';
                $formatDate = fn($date) => $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : '—';
                $formatDecimal = fn($decimal) => is_numeric($decimal) ? number_format($decimal, 0, ',', '.') : '—';
            @endphp

            {{-- Informasi Proyek --}}
            <div x-show="tab === 'info'">
                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <x-view.label label="Project Number" :value="$display($project->project_number)" />
                    <x-view.label label="Project Name" :value="$display($project->project_name)" />

                    <x-view.label label="Categorie Name" :value="$display($project->category->name)" />
                </div>

                <div class="grid md:grid-cols-3 gap-4 mb-4">

                    <x-view.label label="Mandays Engineer" :value="$display($project->mandays_engineer)" />
                    <x-view.label label="Mandays Technician" :value="$display($project->mandays_technician)" />
                </div>

                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <x-view.label label="Target Date" :value="$formatDate($project->target_dates)" />
                </div>

            </div>

            {{-- Informasi Quotation --}}
            <div x-show="tab === 'quotation'" x-cloak>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <x-view.label label="No. Quotation" :value="$display($project->quotation->no_quotation)" />
                    <x-view.label label="Client" :value="$display($project->quotation->client->name)" />
                </div>
                <div class="grid md:grid-cols-2 gap-4 mb-4">

                    <x-view.label label="PO Value" :value="$formatDecimal($project->quotation->po_value)" />
                    <x-view.label label="PO Number" :value="$formatDecimal($project->quotation->po_number)" />
                </div>
                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <x-view.label label="Target Date" :value="$formatDate($project->quotation->po_date)" />
                </div>
            </div>

            {{-- Status Proyek --}}
            <div x-show="tab === 'status'" x-cloak>
                <div class="grid md:grid-cols-4 gap-4 mt-4">
                    <x-view.label label="Client" :value="$display($project->statusProject->name)" />
                </div>
                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    <x-view.label label="Created At" :value="$formatDate($project->created_at)" />
                    <x-view.label label="Updated At" :value="$formatDate($project->updated_at)" />
                </div>
            </div>
        </div>
    </div>
@endsection