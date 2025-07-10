@extends('marketing.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">
        {{ isset($project) ? 'Edit Project' : 'Create Project' }}
    </h2>

    <form action="{{ isset($project) ? route('project.update', $project) : route('project.store') }}" method="POST">
        @csrf
        @if(isset($project)) @method('PUT') @endif

        <input type="hidden" name="status_project_id" value="1">

        {{-- Project Name / Category / Number --}}
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Project Name</label>
                <input type="text" name="project_name" value="{{ old('project_name', $project->project_name ?? '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
                @error('project_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Project Category</label>
                <select name="categories_project_id" class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
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
                <label class="block text-sm font-medium text-gray-700">Project Number</label>
                <input type="text" name="project_number" value="{{ old('project_number', $project->project_number ?? \App\Models\Project::generateProjectNumber()) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 bg-gray-100" readonly>
            </div>
        </div>

        {{-- Quotation Related --}}
        <div class="border rounded p-4 mb-6">
            <h3 class="font-semibold text-md mb-2">Informasi Quotation Terkait</h3>
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quotation</label>
                    <select name="quotations_id" id="quotations_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
                        <option value="">-- Select --</option>
                        @foreach ($quotations as $quotation)
                            <option value="{{ $quotation->id }}"
                                {{ old('quotations_id', $project->quotations_id ?? '') == $quotation->id ? 'selected' : '' }}>
                                {{ $quotation->no_quotation }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Value</label>
                    <input type="text" id="project_value_display"
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1 bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">PO Date</label>
                    <input type="text" id="po_date_display"
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1 bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">PO Number</label>
                    <input type="text" id="po_number_display"
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1 bg-gray-100" readonly>
                </div>
            </div>
        </div>

        {{-- Mandays --}}
        <div class="grid md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Mandays Engineer</label>
                <input type="number" name="mandays_engineer" value="{{ old('mandays_engineer', $project->mandays_engineer ?? '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Mandays Technician</label>
                <input type="number" name="mandays_technician" value="{{ old('mandays_technician', $project->mandays_technician ?? '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
            </div>
        </div>

        {{-- Target Date --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Target Dates</label>
            <input type="date" name="target_dates" value="{{ old('target_dates', $project->target_dates ?? '') }}"
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end mt-8">
            <a href="{{ route('marketing.project') }}" class="text-gray-600 hover:underline mr-6">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                {{ isset($project) ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const quotations = @json($quotations->keyBy('id'));
    const select = document.getElementById('quotations_id');

    function updateQuotationInfo(id) {
        const q = quotations[id];
        document.getElementById('project_value_display').value = q ? 'Rp ' + parseInt(q.project_value || 0).toLocaleString('id-ID') : '-';
        document.getElementById('po_date_display').value = q?.po_date ?? '-';
        document.getElementById('po_number_display').value = q?.po_number ?? '-';
    }

    if (select) {
        select.addEventListener('change', function () {
            updateQuotationInfo(this.value);
        });
        updateQuotationInfo(select.value); // initial
    }
</script>
@endpush
