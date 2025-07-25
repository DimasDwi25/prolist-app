@extends('supervisor.layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200">

        {{-- Page Header --}}
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ isset($project) ? '✏️ Edit Project' : '➕ Create Project' }}
            </h2>
            <a href="{{ route('supervisor.project') }}"
                class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-1 transition">
                ← Back to Projects
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ isset($project) ? route('project.update', $project) : route('project.store') }}" method="POST"
            class="space-y-6">
            @csrf
            @if(isset($project)) @method('PUT') @endif

            <input type="hidden" name="status_project_id" value="1">

            {{-- Project Info --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                    <input type="text" name="project_name" required
                        value="{{ old('project_name', $project->project_name ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    @error('project_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project Category</label>
                    <select name="categories_project_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <option value="">-- Select --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('categories_project_id', $project->categories_project_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categories_project_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project Number</label>
                    <input type="text" name="project_number" readonly
                        class="w-full bg-gray-100 text-gray-500 border border-gray-300 rounded-lg px-3 py-2 shadow-sm"
                        value="{{ old('project_number', $project->project_number ?? \App\Models\Project::generateProjectNumber()) }}">
                </div>
            </div>

            {{-- Quotation Info --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 space-y-4">
                <h3 class="text-md font-semibold text-gray-700">📑 Quotation Details</h3>
                <div class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quotation</label>
                        <select name="quotations_id" id="quotations_id" required
                            class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" aria-placeholder="Select Quotation">
                            <option value="">-- Select Quotation --</option>
                            @foreach ($quotations as $quotation)
                                <option value="{{ $quotation->id }}" {{ old('quotations_id', $project->quotations_id ?? '') == $quotation->id ? 'selected' : '' }}>
                                    {{ $quotation->no_quotation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Value</label>
                        <input type="text" id="project_value_display" readonly
                            class="w-full bg-gray-100 text-gray-500 border border-gray-300 rounded-lg px-3 py-2 shadow-sm">
                    </div>

                    @php
                        use Carbon\Carbon;

                        $poDateFormatted = isset($quotation->po_date)
                            ? Carbon::parse($quotation->po_date)->translatedFormat('d F Y') // Contoh: 17 Juli 2025
                            : '';
                    @endphp
                    <div class="mb-4">
                        <label for="po_date_display" class="block text-sm font-medium text-gray-700 mb-1">PO Date</label>
                        <input type="text" id="po_date_display" name="po_date_display" readonly
                            class="w-full bg-gray-100 text-gray-600 border border-gray-300 rounded-lg px-3 py-2 shadow-sm text-sm"
                            value="{{ $poDateFormatted }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Number</label>
                        <input type="text" id="po_number_display" readonly
                            class="w-full bg-gray-100 text-gray-500 border border-gray-300 rounded-lg px-3 py-2 shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Mandays --}}
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mandays Engineer</label>
                    <input type="number" name="mandays_engineer"
                        value="{{ old('mandays_engineer', $project->mandays_engineer ?? '') }}"
                        class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mandays Technician</label>
                    <input type="number" name="mandays_technician"
                        value="{{ old('mandays_technician', $project->mandays_technician ?? '') }}"
                        class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            {{-- Target Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Dates</label>
                <input type="date" name="target_dates" value="{{ old('target_dates', $project->target_dates ?? '') }}"
                    class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('supervisor.project') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                    {{ isset($project) ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const quotations = @json($quotations->keyBy('id'));
        const select = document.getElementById('quotations_id');

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            if (isNaN(date)) return '-';
            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            }).format(date);
        }

        function updateQuotationInfo(id) {
            const q = quotations[id];
            document.getElementById('project_value_display').value = q
                ? 'Rp ' + parseInt(q.project_value || 0).toLocaleString('id-ID')
                : '-';

            document.getElementById('po_date_display').value = q?.po_date
                ? formatDate(q.po_date)
                : '-';

            document.getElementById('po_number_display').value = q?.po_number ?? '-';
        }

        $(document).ready(function () {
            $('#quotations_id').select2({
                placeholder: '-- Select Quotation --',
                width: '100%',
                allowClear: true
            });

            $('#quotations_id').on('change', function () {
                updateQuotationInfo(this.value);
            });

            updateQuotationInfo($('#quotations_id').val());
        });
    </script>
@endpush