@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $editing ?? false ? 'Edit Project Details' : 'Create New Project' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $editing ?? false ? 'Update existing project information' : 'Set up a new project for your team' }}
                </p>
            </div>
            
            <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-2">
                <p class="text-xs font-medium text-blue-600">PROJECT NUMBER</p>
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-800">
                        @if($editing ?? false)
                            {{ $project->project_number }}
                        @else
                            {{ \App\Models\Project::generateProjectNumber(\App\Models\Project::generatePnNumber()) }}
                        @endif
                    </p>
                    <button type="button" onclick="copyToClipboard(this.previousElementSibling.textContent.trim())" 
                        class="text-blue-400 hover:text-blue-600 transition" title="Copy to clipboard">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-700">There were {{ $errors->count() }} errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-600">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ optional($project)->exists ? route('project.update', $project->pn_number) : route('project.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(optional($project)->exists) @method('PUT') @endif

            <input type="hidden" name="status_project_id" value="{{ $project->status_project_id ?? 1 }}">

            {{-- Quotation Section --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Quotation Information</h3>
                </div>
                
                <p class="text-sm text-gray-600 ml-11 -mt-2">Link this project to an existing quotation for automatic data population</p>
                
                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    {{-- Client Select --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Client</label>
                        <select id="client_select"
                            class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose a client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" 
                                    @if(isset($project) && $project->quotation && $project->quotation->client_id == $client->id) selected @endif>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Quotation Select --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Quotation</label>
                        <select name="quotations_id" id="quotations_id"
                            class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select quotation...</option>
                            @if(isset($project) && $project->quotation)
                                <option value="{{ $project->quotation->quotation_number }}" selected>
                                    {{ $project->quotation->no_quotation }}
                                </option>
                            @endif
                        </select>
                    </div>

                    {{-- Quotation Details --}}

                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <label class="block text-xs font-medium text-gray-500 mb-1">QUOTATION TITLE</label>
                        <div class="text-lg font-semibold text-gray-800" id="quotation_title_display">
                            @if(isset($project) && $project->quotation)
                                {{ $project->quotation->title_quotation }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <label class="block text-xs font-medium text-gray-500 mb-1">CLIENT NAME</label>
                        <div class="text-lg font-semibold text-gray-800" id="quotation_client_display">
                            @if(isset($project) && $project->quotation)
                                {{ $project->quotation->client->name }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <label class="block text-xs font-medium text-gray-500 mb-1">QUOTATION VALUE</label>
                        <div class="text-lg font-semibold text-gray-800" id="project_value_display">
                            @if(isset($project) && $project->quotation)
                                {{ number_format($project->quotation->quotation_value, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <label class="block text-xs font-medium text-gray-500 mb-1">QUOTATION DATE</label>
                        <div class="text-lg font-semibold text-gray-800" id="po_date_display">
                            @if(isset($project) && $project->quotation)
                                {{ \Carbon\Carbon::parse($project->quotation->quotation_date)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Selection -->
            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Client
                    <span class="text-xs text-gray-500 ml-1">(Select from existing clients)</span>
                </label>
                <select id="client_id" name="client_id" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id ?? '') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Project Information Section --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Project Details</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Name*</label>
                        <input type="text" name="project_name" required
                            value="{{ old('project_name', $project->project_name ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                            placeholder="e.g. Website Redesign Project">
                        <p class="mt-1 text-xs text-gray-500">Enter a descriptive project name</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Category*</label>
                        <select name="categories_project_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            <option value="">Select category...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" 
                                    {{ old('categories_project_id', $project->categories_project_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Number</label>
                        <div class="relative">
                            <input type="text" name="project_number" readonly
                                class="w-full bg-gray-100 text-gray-600 border border-gray-300 rounded-lg px-3 py-2 shadow-sm font-mono"
                                value="{{ old('project_number', $project->project_number ?? \App\Models\Project::generateProjectNumber(\App\Models\Project::generatePnNumber())) }}">
                            <button type="button" onclick="copyToClipboard(this.previousElementSibling)" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Automatically generated</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Completion Date*</label>
                        <input type="date" name="target_dates" 
                            value="{{ old('target_dates', isset($project) && $project->target_dates ? $project->target_dates->format('Y-m-d') : '') }}"
                            class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>
                </div>
            </div>

            {{-- Resource Planning Section --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Resource Planning</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Engineer Mandays</label>
                        <div class="relative">
                            <input type="number" name="mandays_engineer"
                                value="{{ old('mandays_engineer', $project->mandays_engineer ?? '') }}"
                                class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0">
                            
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Estimated workdays required by engineers</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Technician Mandays</label>
                        <div class="relative">
                            <input type="number" name="mandays_technician"
                                value="{{ old('mandays_technician', $project->mandays_technician ?? '') }}"
                                class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0">
                            
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Estimated workdays required by technicians</p>
                    </div>
                </div>
            </div>

            {{-- Order Information Section --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Order Information</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Number</label>
                        <input type="text" name="po_number"
                            value="{{ old('po_number', $project->po_number ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                            placeholder="e.g. PO-2023-001">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Date</label>
                        <input type="date" name="po_date"
                            value="{{ old('po_date', isset($project) && $project->po_date ? $project->po_date->format('Y-m-d') : '') }}"
                            class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Value (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">Rp</span>
                            <input type="text" step="0.01" name="po_value" id="po_value"
                                value="{{ old('po_value', $project->po_value ?? '') }}"
                                class="form-input w-full border border-gray-300 rounded-lg px-3 py-2 pl-8 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0.00">

                            <!-- Hidden input to store the raw numeric value -->
                            <input type="hidden" id="po_value_raw" name="po_value_raw" 
                                value="{{ old('po_value_raw', optional($project)->po_value) }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sales Week</label>
                        <input type="text" name="sales_weeks" readonly
                            value="{{ old('sales_weeks', $project->sales_weeks ?? '') }}"
                            class="w-full bg-gray-100 text-gray-600 border border-gray-300 rounded-lg px-3 py-2 shadow-sm">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_confirmation_order" name="is_confirmation_order" value="1" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                {{ old('is_confirmation_order', $project->is_confirmation_order ?? false) ? 'checked' : '' }}>
                            <label for="is_confirmation_order" class="ml-2 block text-sm text-gray-700">
                                Confirmation Order
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_variant_order" name="is_variant_order" value="1" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                {{ old('is_variant_order', $project->is_variant_order ?? false) ? 'checked' : '' }}>
                            <label for="is_variant_order" class="ml-2 block text-sm text-gray-700">
                                Variant Order
                            </label>
                        </div>
                    </div>

                    <div id="parent_pn_container" class="{{ old('is_variant_order', $project->is_variant_order ?? false) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Project</label>
                        <select id="parent_pn_number" name="parent_pn_number" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            <option value="">Select parent project...</option>
                            @foreach ($projects as $parent)
                                @if(!isset($project) || $project->pn_number != $parent->pn_number)
                                <option value="{{ $parent->pn_number }}" {{ old('parent_pn_number', $project->parent_pn_number ?? '') == $parent->pn_number ? 'selected' : '' }}>
                                    {{ $parent->project_number }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                        
                        <div id="parent_project_info" class="mt-3 p-3 border rounded bg-gray-50 text-sm text-gray-700 hidden">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="font-medium text-gray-500">Project Name:</p>
                                    <p id="parent_name">-</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Category:</p>
                                    <p id="parent_category">-</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Client:</p>
                                    <p id="parent_client">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex flex-col-reverse md:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('supervisor.project') }}"
                    class="px-6 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $editing ?? false ? 'Update Project' : 'Create Project' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5"></script>
<script>
    const quotations = @json($quotations);
    const quotationsByClient = {};

    quotations.forEach(q => {
        if (!quotationsByClient[q.client_id]) {
            quotationsByClient[q.client_id] = [];
        }
        quotationsByClient[q.client_id].push(q);
    });

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    function formatCurrency(amount) {
        if (!amount) return '-';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function updateQuotationInfo(id) {
        const q = quotations.find(q => String(q.quotation_number) === String(id));
        
        // Update quotation value display
        document.getElementById('project_value_display').textContent = q ? formatCurrency(q.quotation_value) : '-';
        
        // Update quotation date display
        document.getElementById('po_date_display').textContent = q ? formatDate(q.quotation_date) : '-';
        
        // Update quotation title display (add this element to your HTML)
        const titleDisplay = document.getElementById('quotation_title_display');
        if (titleDisplay) {
            titleDisplay.textContent = q ? q.title_quotation : '-';
        }
        
        // Update client name display (add this element to your HTML)
        const clientDisplay = document.getElementById('quotation_client_display');
        if (clientDisplay) {
            clientDisplay.textContent = q ? (q.client?.name || '-') : '-';
        }
    }

    function populateQuotations(list) {
        $('#quotations_id').empty().append(`<option value="">Select quotation...</option>`);
        list.forEach(q => {
            $('#quotations_id').append(`
                <option value="${q.quotation_number}" data-value="${q.quotation_value}" data-date="${q.quotation_date}"
                    ${(typeof q.selected !== 'undefined' && q.selected) ? 'selected' : ''}>
                    ${q.no_quotation}
                </option>
            `);
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            const originalText = text;
            const tempElement = document.createElement('input');
            tempElement.value = text;
            document.body.appendChild(tempElement);
            tempElement.select();
            document.execCommand('copy');
            document.body.removeChild(tempElement);
            
            // Show temporary message
            const button = event.target.closest('button');
            if (button) {
                const originalHTML = button.innerHTML;
                button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>`;
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                }, 2000);
            }
        });
    }

    $(document).ready(function () {

        // Initialize Select2 for client selection
        $('#client_id').select2({
            placeholder: "Search for a client...",
            allowClear: true,
            width: '100%'
        });

        @if(isset($project) && $project->client)
            // Pre-select the client if editing
            var clientOption = new Option("{{ $project->client->name }}", "{{ $project->client->id }}", true, true);
            $('#client_id').append(clientOption).trigger('change');
        @endif

        // Initialize select2
        $('#client_select, #quotations_id, #parent_pn_number').select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true
        });

        // If editing and has quotation, populate client and quotations
        @if(isset($project) && $project->quotation)
            const clientId = {{ $project->quotation->client_id }};
            const clientQuotations = quotationsByClient[clientId] || [];
            
            // Mark the selected quotation
            clientQuotations.forEach(q => {
                if (q.quotation_number === {{ $project->quotation->quotation_number }}) {
                    q.selected = true;
                }
            });
            
            populateQuotations(clientQuotations);
            $('#client_select').val(clientId).trigger('change');
            $('#quotations_id').val({{ $project->quotation->quotation_number }}).trigger('change');
        @else
            // Populate all quotations initially for create form
            populateQuotations(quotations);
        @endif

        // Filter quotations based on client selection
        $('#client_select').on('change', function () {
            const clientId = $(this).val();
            if (!clientId) {
                populateQuotations(quotations);
            } else {
                populateQuotations(quotationsByClient[clientId] || []);
            }
            $('#quotations_id').val(null).trigger('change');
        });

        // Update info when quotation is selected
        $('#quotations_id').on('change', function () {
            updateQuotationInfo($(this).val());
        });

        // Handle confirmation order checkbox
        $('#is_confirmation_order').on('change', function () {
            const isCO = $(this).is(':checked');
            $.get('/projects/generate-number', { is_co: isCO }, function (data) {
                $('input[name="project_number"]').val(data.project_number);
            });
        });

        // Toggle parent project field visibility
        $('#is_variant_order').on('change', function () {
            $('#parent_pn_container').toggleClass('hidden', !$(this).is(':checked'));
            if (!$(this).is(':checked')) {
                $('#parent_pn_number').val(null).trigger('change');
                $('#parent_project_info').addClass('hidden');
            }
        });

        // Load parent project info
        $('#parent_pn_number').on('change', function () {
            const pnNumber = $(this).val();
            if (!pnNumber) {
                $('#parent_project_info').addClass('hidden');
                return;
            }

            $.get(`/projects/info/${pnNumber}`, function (data) {
                $('#parent_name').text(data.project_name || '-');
                $('#parent_category').text(data.category?.name || '-');
                $('#parent_client').text(data.quotation?.client?.name || '-');
                $('#parent_project_info').removeClass('hidden');
            }).fail(function() {
                $('#parent_project_info').addClass('hidden');
            });
        });

        // Auto-generate week number when PO date changes
        $('input[name="po_date"]').on('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                const weekNumber = getWeekOfYear(date);
                $('input[name="sales_weeks"]').val(`${date.getFullYear()}-W${weekNumber}`);
            }
        });

        function getWeekOfYear(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        // Initialize sales week if po_date exists
        @if(isset($project) && $project->po_date)
            const initialDate = new Date('{{ $project->po_date->format("Y-m-d") }}');
            const weekNumber = getWeekOfYear(initialDate);
            $('input[name="sales_weeks"]').val(`${initialDate.getFullYear()}-W${weekNumber}`);
        @endif

        // Inisialisasi AutoNumeric pada quotation_value
        const poValue = new AutoNumeric('#po_value', {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            currencySymbol: 'Rp ',
            currencySymbolPlacement: 'p', // prefix
            unformatOnSubmit: true // biar otomatis kirim angka mentah kalau submit form
        });

        // Update hidden field setiap kali value berubah
        $('#po_value').on('keyup change', function () {
            $('#po_value_raw').val(poValue.getNumber()); 
        });  
    });
</script>
@endpush