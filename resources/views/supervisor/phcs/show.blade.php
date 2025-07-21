@extends(match(Auth::user()->role->name) {
    'super_admin' => 'admin.layouts.app',
    'supervisor marketing' => 'supervisor.layouts.app',
    'engineer' => 'engineer.layouts.app',
    default => 'layouts.app', // fallback jika role tidak cocok
})

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 md:p-10 rounded-lg shadow space-y-8">
        {{-- Header --}}
        <div class="flex justify-between items-center flex-wrap mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📁 Project Handover Checklist (PHC)</h2>
                <p class="text-sm text-gray-500">Informasi lengkap dan dokumen yang disiapkan untuk serah terima proyek</p>
            </div>
            <div class="flex flex-wrap gap-2">
                
                <a href="{{ route('phc.edit', $phc->id) }}"
                    class="inline-flex items-center bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
                    ✏️ Edit
                </a>
                <a href="{{ route('supervisor.project.show', $phc->project_id) }}"
                    class="inline-flex items-center bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 text-sm">
                    ← Kembali
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'info' }" class="space-y-6">
            <div class="border-b pb-2">
                <nav class="flex flex-wrap gap-4">
                    <button @click="tab = 'info'"
                        :class="tab === 'info' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
                        class="px-4 py-2 font-medium">
                        📋 Information
                    </button>
                    <button @click="tab = 'handover'"
                        :class="tab === 'handover' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
                        class="px-4 py-2 font-medium">
                        ✅ Handover Checklist
                    </button>
                    <button @click="tab = 'docs'"
                        :class="tab === 'docs' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
                        class="px-4 py-2 font-medium">
                        📄 Document Preparation List
                    </button>
                </nav>
            </div>

            {{-- Informasi Umum --}}
            <div x-show="tab === 'info'" x-cloak class="space-y-4">
                @php
                    $display = fn($value) => $value ?: '—';
                    $formatDate = fn($date) => $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : '—';
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-view.label label="Project Name" :value="$display($phc->project->project_name)" class="w-full" />
                    </div>
                    <x-view.label label="Project Number" :value="$display($phc->project->project_number)" />
                    <x-view.label label="Handover Date" :value="$formatDate($phc->handover_date)" />
                    <x-view.label label="Start Date" :value="$formatDate($phc->start_date)" />
                    <x-view.label label="Target Finish Date" :value="$formatDate($phc->target_finish_date)" />
                    <x-view.label label="Quotation No" :value="$display($phc->project->quotation->no_quotation ?? '')" />
                    <x-view.label label="Quotation Date" :value="$formatDate($phc->project->quotation->quotation_date ?? '')" />
                    <x-view.label label="PO Number" :value="$display($phc->project->quotation->po_number ?? '')" />
                    <x-view.label label="PO Date" :value="$formatDate($phc->project->quotation->po_date ?? '')" />
                    <x-view.label label="Client Name" :value="$display($phc->client_name)" />
                    <x-view.label label="Client Mobile" :value="$display($phc->client_mobile)" />
                    <x-view.label label="Client Office Address" :value="$display($phc->client_reps_office_address)" />
                    <x-view.label label="Site Representative" :value="$display($phc->client_site_representatives)" />
                    <x-view.label label="Site Phone" :value="$display($phc->site_phone_number)" />
                    <x-view.label label="Site Address" :value="$display($phc->client_site_address)" />
                    <x-view.label label="PIC Marketing" :value="$display($phc->picMarketing->name ?? '')" />
                    <x-view.label label="PIC Engineer" :value="$display($phc->picEngineering->name ?? '')" />
                    <x-view.label label="HO Marketing" :value="$display($phc->hoMarketing->name ?? '')" />
                    <x-view.label label="HO Engineer" :value="$display($phc->hoEngineering->name ?? '')" />
                </div>

                <div>
                    <p class="text-sm text-gray-500">Notes</p>
                    <div class="bg-gray-100 p-4 rounded text-sm text-gray-700 whitespace-pre-line">
                        {{ $phc->notes ?: '— Tidak ada catatan —' }}
                    </div>
                </div>
            </div>

            {{-- Handover Checklist --}}
            <div x-show="tab === 'handover'" x-cloak class="space-y-4">
                <x-phc-check label="Costing by Marketing" :value="$phc->costing_by_marketing" />
                <x-phc-check label="BOQ" :value="$phc->boq" />
                <x-phc-badge label="Retention" :value="$phc->retention_applicability" :detail="$phc->retention" />
                <x-phc-badge label="Warranty" :value="$phc->warranty" :detail="$phc->warranty_detail" />
                <x-phc-badge label="Penalty" :value="$phc->penalty" :detail="$phc->penalty_detail" />
            </div>

            {{-- Document Preparation --}}
            <div x-show="tab === 'docs'" x-cloak class="space-y-4">
                @php
                    $docs = [
                        'scope_of_work_approval' => 'Scope of Work Approval',
                        'organization_chart' => 'Organization Chart',
                        'project_schedule' => 'Project Schedule',
                        'progress_claim_report' => 'Progress Claim Report',
                        'component_approval_list' => 'Component Approval List',
                        'design_approval_draw' => 'Design Approval Draw',
                        'shop_draw' => 'Shop Draw',
                        'fat_sat_forms' => 'FAT / SAT Forms',
                        'daily_weekly_progress_report' => 'Daily/Weekly Progress Report',
                        'do_packing_list' => 'DO / Packing List',
                        'site_testing_commissioning_report' => 'Testing & Commissioning Report',
                        'as_build_draw' => 'As Built Draw',
                        'manual_documentation' => 'Manual Documentation',
                        'accomplishment_report' => 'Accomplishment Report',
                        'client_document_requirements' => 'Client Document Requirements',
                        'job_safety_analysis' => 'Job Safety Analysis',
                        'risk_assessment' => 'Risk Assessment',
                        'tool_list' => 'Tool List',
                    ];
                @endphp

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($docs as $field => $label)
                        <x-phc-check :label="$label" :value="$phc->$field" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
