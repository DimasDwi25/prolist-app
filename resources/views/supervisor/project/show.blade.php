@extends('supervisor.layouts.app')

@section('content')
    @php
        $role = Auth::user()->role->name ?? '';
        $phc = $project->phc;
        $pendingApprovals = $phc?->approvals()->where('status', 'pending')->with('user')->get() ?? collect();
        $display = fn($value) => $value ?: '—';
        $formatDate = fn($date) => $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : '—';
        $formatDecimal = fn($decimal) => is_numeric($decimal) ? number_format($decimal, 0, ',', '.') : '—';
    @endphp

    <div class="max-w-6xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-200 space-y-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">📁 View Project</h2>
                <a href="{{ route('supervisor.project') }}"
                    class="text-sm text-blue-500 hover:text-blue-700 hover:underline">
                    ← Back to Projects
                </a>
            </div>

            <div class="flex flex-wrap gap-3 items-start">
                {{-- Project Edit --}}
                <a href="{{ route('project.edit', $project) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm shadow-md">
                    ✏️ Edit Project
                </a>

                {{-- PHC Section --}}
                @if ($phc)
                    <a href="{{ route('phc.edit', $phc->id) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm shadow-md">
                        ✏️ Edit PHC
                    </a>

                    <a href="{{ route('phc.show', $phc->id) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm shadow-md">
                        👁️ View PHC
                    </a>
                @else
                    <a href="{{ route('phc', $project->id) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm shadow-md">
                        ➕ Create PHC
                    </a>
                @endif

                {{-- View Logs --}}
                <a href="{{ route('supervisor.projects.logs', $project->id) }}"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-xl text-sm shadow-md">
                    📋 View Logs
                </a>
            </div>
        </div>

        {{-- PHC Approval Status Info --}}
        @if ($phc && $phc->status === 'ready')
            <div class="w-full bg-green-50 border border-green-200 text-green-800 rounded-xl p-4">
                <p class="font-semibold mb-2">✅ PHC Status: <span class="text-green-900">Ready</span></p>
                <p class="text-sm">Semua user telah memberikan approval. PHC ini siap untuk tahap selanjutnya.</p>
            </div>
        @elseif ($phc && $pendingApprovals->isNotEmpty())
            <div class="w-full bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4">
                <p class="font-semibold mb-2">⏳ PHC Status: <span class="text-yellow-900">Pending Approval</span></p>
                <p class="text-sm">Menunggu approval dari:</p>
                <ul class="list-disc pl-6 mt-1 text-sm space-y-1">
                    @foreach ($pendingApprovals as $approval)
                        <li>{{ $approval->user->name ?? 'User tidak ditemukan' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        {{-- Tabs --}}
        <div x-data="{ tab: 'info' }">
            <nav class="flex space-x-4 text-sm font-medium border-b pb-2">
                <button class="px-4 py-2 rounded-t-md focus:outline-none"
                    :class="tab === 'info' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600 font-semibold' : 'hover:text-blue-500'"
                    @click="tab = 'info'">
                    📁 Informasi Proyek
                </button>
                <button class="px-4 py-2 rounded-t-md focus:outline-none"
                    :class="tab === 'quotation' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600 font-semibold' : 'hover:text-blue-500'"
                    @click="tab = 'quotation'">
                    📄 Informasi Quotation
                </button>
                <button class="px-4 py-2 rounded-t-md focus:outline-none"
                    :class="tab === 'status' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600 font-semibold' : 'hover:text-blue-500'"
                    @click="tab = 'status'">
                    📊 Status Proyek
                </button>
            </nav>

            {{-- Info Project --}}
            <div x-show="tab === 'info'" x-cloak class="space-y-4 mt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-view.label label="Project Number" :value="$display($project->project_number)" />
                    <x-view.label label="Project Name" :value="$display($project->project_name)" />
                    <x-view.label label="Categorie Name" :value="$display($project->category->name ?? null)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-view.label label="Mandays Engineer" :value="$display($project->mandays_engineer)" />
                    <x-view.label label="Mandays Technician" :value="$display($project->mandays_technician)" />
                    <x-view.label label="Target Date" :value="$formatDate($project->target_dates)" />
                </div>
            </div>

            {{-- Info Quotation --}}
            <div x-show="tab === 'quotation'" x-cloak class="space-y-4 mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-view.label label="No. Quotation" :value="$display($project->quotation->no_quotation ?? null)" />
                    <x-view.label label="Client" :value="$display($project->quotation->client->name ?? null)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-view.label label="PO Value" :value="$formatDecimal($project->quotation->po_value ?? null)" />
                    <x-view.label label="PO Number" :value="$display($project->quotation->po_number ?? null)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-view.label label="PO Date" :value="$formatDate($project->quotation->po_date ?? null)" />
                </div>
            </div>

            {{-- Info Status --}}
            <div x-show="tab === 'status'" x-cloak class="space-y-4 mt-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <x-view.label label="Status Proyek" :value="$display($project->statusProject->name ?? null)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-view.label label="Created At" :value="$formatDate($project->created_at)" />
                    <x-view.label label="Updated At" :value="$formatDate($project->updated_at)" />
                </div>
            </div>
        </div>

        {{-- Project Logs --}}
        <div class="pt-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">📜 Project Logs</h3>
            <div class="bg-gray-50 rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="hidden md:block">
                    @livewire('log.log-table', ['projectId' => $project->id])
                </div>
            </div>
        </div>
    </div>
@endsection