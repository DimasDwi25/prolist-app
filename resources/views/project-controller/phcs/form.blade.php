@php
    $roleLayouts = [
        'project controller'     => 'project-controller.layouts.app',
        'engineer'     => 'engineer.layouts.app',
        'engineering_manager'         => 'project-manager.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-8 text-center">
        ✏️ Update Project Handover Checklist (PHC)
    </h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('engineer.project.show', $phc->project_id) }}"
           class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            ⬅️ Back to Project
        </a>
    </div>

    <form action="{{ route('engineer.phc.update', $phc) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="project_id" value="{{ $phc->project_id }}">

        {{-- AlpineJS Component --}}
        <div x-data="phcForm()" x-cloak>
            {{-- Step Indicator --}}
            <div class="mb-4 md:mb-6 text-center text-gray-600 font-medium text-sm md:text-base">
                <template x-if="step === 1"><span>🔹 <strong>Step 1 of 3:</strong> General Information (View Only)</span></template>
                <template x-if="step === 2"><span>📋 <strong>Step 2 of 3:</strong> Handover Checklist (View Only)</span></template>
                <template x-if="step === 3"><span>📄 <strong>Step 3 of 3:</strong> Document Preparation (Editable)</span></template>
            </div>

            {{-- Step Tabs --}}
            <div class="flex flex-col md:flex-row justify-center mb-6 gap-3 md:space-x-4">
                <template x-for="i in [1,2,3]" :key="i">
                    <button type="button" @click="setStep(i)" 
                        :class="{
                            'bg-blue-600 text-white': step === i,
                            'bg-gray-200 text-gray-700': step !== i,
                            'border-2 border-blue-400': step === 3
                        }"
                        class="px-4 py-2 rounded-md font-medium transition w-full md:w-40 text-sm md:text-base relative">
                        <span x-text="i === 1 ? '1️⃣ Information' : (i === 2 ? '2️⃣ Checklist' : '3️⃣ Documents')"></span>
                        <template x-if="i === 3">
                            <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">EDIT</span>
                        </template>
                    </button>
                </template>
            </div>

            {{-- STEP 1: General Information (Partially Editable) --}}
            <div x-show="step === 1" x-transition class="space-y-5 md:space-y-6">
                {{-- Project Information Section --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Project Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <x-input.text name="project_name" label="Project" disabled :value="$phc->project->project_name" />
                        <x-input.text name="project_number" label="PN No" disabled :value="$phc->project->project_number" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <x-input.text name="handover_date" label="Handover Date" disabled 
                            :value="$phc->handover_date ? \Illuminate\Support\Carbon::parse($phc->handover_date)->format('d M Y') : ''" />
                        <x-input.text name="start_date" label="Start Date" disabled 
                            :value="$phc->start_date ? \Illuminate\Support\Carbon::parse($phc->start_date)->format('d M Y') : ''" />
                        <x-input.text name="target_finish_date" label="Target Finish Date" disabled 
                            :value="$phc->target_finish_date ? \Illuminate\Support\Carbon::parse($phc->target_finish_date)->format('d M Y') : ''" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input.text name="no_quotation" label="Quotation No" disabled :value="$phc->no_quotation ?? $phc->project->quotation->no_quotation" />
                        <x-input.text name="quotation_date" label="Quotation Date" disabled 
                            :value="optional($phc->project->quotation->quotation_date)->format('d M Y')" />
                    </div>
                </div>

                {{-- Client Information Section --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Client Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <x-input.text name="client_pic_name" label="Client Name" :value="$phc->client_pic_name" disabled />
                        <x-input.text name="client_mobile" label="Client Mobile" :value="$phc->client_mobile" disabled />
                        <x-input.text name="client_reps_office_address" label="Office Address" :value="$phc->client_reps_office_address" disabled />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-input.text name="client_site_address" label="Site Address" :value="$phc->client_site_address" disabled />
                        <x-input.text name="client_site_representatives" label="Site Representative" :value="$phc->client_site_representatives" disabled />
                        <x-input.text name="site_phone_number" label="Site Phone Number" :value="$phc->site_phone_number ?? '-'" disabled />
                    </div>
                </div>

                {{-- Team Information Section --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Team Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <x-input.text name="ho_engineering" label="HO Engineer" :value="$phc->hoEngineering->name ?? '-'" disabled />
                        
                        {{-- Editable PIC Engineer --}}
                        <div>
                            <label for="pic_engineering_id" class="block text-sm font-medium text-gray-700 mb-1">
                                PIC Engineer
                            </label>
                            <select id="pic_engineering_id" name="pic_engineering_id" class="select2 w-full" wire:ignore>
                                <option value="">-- Select PIC Engineer --</option>
                                <optgroup label="Engineer">
                                    @foreach ($users->filter(fn($u) => 
                                        $u->role && in_array(strtolower($u->role->name), ['engineer', 'supervisor engineer', 'project manager', 'project controller'])
                                    ) as $user)
                                        <option value="{{ $user->id }}" {{ old('pic_engineering_id', $phc->pic_engineering_id ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input.text name="ho_marketing" label="HO Marketing" :value="$phc->hoMarketing->name ?? '-'" disabled />
                        
                        {{-- Editable PIC Marketing --}}
                        <x-input.text name="pic_marketing" label="PIC Marketing" :value="$phc->picMarketing->name ?? '-'" disabled />
                    </div>
                </div>

                {{-- Notes Section --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Additional Notes
                    </h3>
                    <div>
                        <label class="text-sm text-gray-600">Notes</label>
                        <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2 text-sm bg-gray-100" disabled>{{ $phc->notes }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="setStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded flex items-center">
                        Next: Checklist
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- STEP 2: Handover Checklist (Read-only) --}}
            <div x-show="step === 2" x-transition class="space-y-5 md:space-y-6">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">📋 Handover Checklist Items</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        @foreach([
                            'costing_by_marketing' => 'Costing by Marketing',
                            'boq' => 'Bill of Quantity (BOQ)',
                            'retention_applicability' => 'Retention Applicability',
                            'warranty' => 'Warranty',
                            'penalty' => 'Penalty',
                        ] as $key => $label)
                            <div x-data="{ applicable: '{{ $phc->$key ? 'A' : 'NA' }}' }"
                                class="p-4 border rounded-md bg-white">
                                <div class="flex items-center mb-2">
                                    <div :class="{
                                        'bg-green-100 text-green-800': applicable === 'A',
                                        'bg-gray-100 text-gray-800': applicable === 'NA'
                                    }" class="px-3 py-1 rounded-full text-xs font-medium">
                                        <span x-text="applicable === 'A' ? 'Applicable' : 'Not Applicable'"></span>
                                    </div>
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
                                </div>
                                
                                @if (in_array($key, ['retention_applicability', 'warranty', 'penalty']) && $phc->$key)
                                    <div x-show="applicable === 'A'" class="mt-2 pl-2 border-l-4 border-blue-200" x-transition>
                                        <p class="text-sm text-gray-600 mb-1">Details:</p>
                                        <p class="text-sm font-medium">
                                            @php
                                                $detailField = $key == 'retention_applicability' ? 'retention' :
                                                            ($key == 'warranty' ? 'warranty_detail' : 'penalty_detail');
                                            @endphp
                                            {{ $phc->$detailField }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between pt-4 gap-3">
                    <button type="button" @click="setStep(1)"
                            class="px-5 py-2 border rounded text-sm md:text-base w-full md:w-auto flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Information
                    </button>
                    <button type="button" @click="setStep(3)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm md:text-base w-full md:w-auto flex items-center justify-center">
                        Next to Documents
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- STEP 3: Document Preparation (Editable) --}}
            <div x-show="step === 3" x-transition class="space-y-5 md:space-y-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-blue-800">You can edit the document preparation status below</h3>
                    </div>
                </div>

                @foreach([
                    '📐 Design & Drawings' => [
                        'scope_of_work_approval' => 'Scope of Work Approval',
                        'design_approval_draw' => 'Design Approval Drawings',
                        'shop_draw' => 'Shop Drawing',
                        'as_build_draw' => 'As-Built Drawings',
                    ],
                    '📋 Reports & Forms' => [
                        'project_schedule' => 'Project Schedule',
                        'progress_claim_report' => 'Progress Claim Report',
                        'fat_sat_forms' => 'FAT/SAT Forms',
                        'daily_weekly_progress_report' => 'Daily/Weekly Progress Report',
                        'site_testing_commissioning_report' => 'Testing & Commissioning Report',
                        'accomplishment_report' => 'Accomplishment Report',
                    ],
                    '🧰 Supporting Documents' => [
                        'organization_chart' => 'Organization Chart',
                        'component_approval_list' => 'Component Approval List',
                        'do_packing_list' => 'DO / Packing List',
                        'manual_documentation' => 'Manual & Documentation',
                        'client_document_requirements' => 'Client Document Requirements',
                        'job_safety_analysis' => 'Job Safety Analysis',
                        'risk_assessment' => 'Risk Assessment',
                        'tool_list' => 'Tool List',
                    ],
                ] as $groupTitle => $fields)
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <h4 class="font-medium text-gray-700 text-lg mb-4 pb-2 border-b flex items-center">
                            <span class="mr-2">{{ $groupTitle }}</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($fields as $key => $label)
                                @php $default = old($key, $phc->$key ? 'A' : 'NA'); @endphp

                                @if ($key === 'scope_of_work_approval')
                                    <div x-data="{ sow: '{{ $default }}' }" class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                        <div class="flex flex-wrap gap-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="A" x-model="sow"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="ml-2 text-sm">Applicable</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="NA" x-model="sow"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="ml-2 text-sm">N/A</span>
                                            </label>
                                        </div>

                                        {{-- Tombol Modal SOW --}}
                                        <template x-if="sow === 'A'">
                                            <div class="mt-2">       
                                                <button type="button"
                                                        onclick="Livewire.dispatch('openModal')"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm w-full md:w-auto">
                                                    + Tambah SOW
                                                </button>
                                               
                                            </div>
                                        </template>

                                        {{-- Modal SOW --}}
                                        <div class="w-full">
                                            
                                                @livewire('supervisor-marketing.scope-of-work-form-modal', ['projectId' => $project->pn_number])
                                           
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                        <div class="flex flex-wrap gap-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="A" @checked($default === 'A')
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="ml-2 text-sm">Applicable</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="NA" @checked($default === 'NA')
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="ml-2 text-sm">Not Applicable</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex flex-col md:flex-row justify-between pt-4 gap-3">
                    <button type="button" @click="setStep(2)"
                            class="px-5 py-2 border rounded text-sm md:text-base w-full md:w-auto flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Checklist
                    </button>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded text-sm md:text-base w-full md:w-auto flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Document Status
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: '-- Select --',
            width: '100%',
            minimumResultsForSearch: 5
        });
    });
</script>
@endpush

{{-- AlpineJS for step control --}}
<script>
function phcForm() {
    return {
        step: 1,
        setStep(i) { 
            this.step = i; 
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Highlight the editable step
            if (i === 3) {
                setTimeout(() => {
                    const editableSection = document.querySelector('[x-show="step === 3"]');
                    editableSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }
    }
}
</script>
@endsection