@extends('engineer.layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold">View Project</h2>
                <a href="{{ route('engineer.project') }}" class="text-sm text-gray-600 hover:underline">← Back to
                    Projects</a>
            </div>

            <div class="space-x-2">

                @if ($project->phc)
                    <a href="{{ route('phc.show', $project->phc->id) }}"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        👁️ View PHC
                    </a>
                @endif

               
                {{-- Tombol View Log --}}
                <a href="{{ route('projects.logs', $project->id) }}"
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
                        :class="tab === 'info' ? 'border-b-2 border-blue-600 text-blue-600' : ''" @click="tab = 'info'">📁
                        Informasi Proyek</button>
                    <button class="px-4 py-2 font-medium"
                        :class="tab === 'quotation' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
                        @click="tab = 'quotation'">📄 Informasi Quotation</button>
                    <button class="px-4 py-2 font-medium"
                        :class="tab === 'status' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
                        @click="tab = 'status'">📊 Status Proyek</button>
                </nav>
            </div>

            {{-- Informasi Proyek --}}
            <div x-show="tab === 'info'">
                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="text-sm text-gray-600">Project Number</label>
                        <p class="text-lg font-semibold text-blue-600">{{ $project->project_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Project Name</label>
                        <p>{{ $project->project_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Kategori</label>
                        <p>{{ $project->category->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-sm text-gray-600">Mandays Engineer</label>
                        <p>{{ $project->mandays_engineer ?? '— Tidak ada data —' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Mandays Technician</label>
                        <p>{{ $project->mandays_technician ?? '— Tidak ada data —' }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Target Date</label>
                    <p>{{ \Carbon\Carbon::parse($project->target_dates)->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            {{-- Informasi Quotation --}}
            <div x-show="tab === 'quotation'" x-cloak>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-sm text-gray-600">No. Quotation</label>
                        <p class="text-yellow-600 font-semibold">{{ $project->quotation->no_quotation ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Client</label>
                        <p>{{ $project->quotation->client->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-sm text-gray-600">Project Value</label>
                        <p class="text-green-700 font-medium">
                            Rp {{ number_format($project->quotation->project_value ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">PO Number</label>
                        <p>{{ $project->quotation->po_number ?? '-' }}</p>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-gray-600">PO Date</label>
                    <p>{{ $project->quotation->po_date ? \Carbon\Carbon::parse($project->quotation->po_date)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            {{-- Status Proyek --}}
            <div x-show="tab === 'status'" x-cloak>
                <div>
                    <label class="text-sm text-gray-600">Status</label>
                    <span class="inline-block px-3 py-1 rounded bg-blue-100 text-blue-800 text-sm font-semibold">
                        {{ $project->statusProject->name ?? 'Pending' }}
                    </span>
                </div>
                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="text-sm text-gray-600">Created At</label>
                        <p>{{ $project->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Updated At</label>
                        <p>{{ $project->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs" defer></script>
@endpush