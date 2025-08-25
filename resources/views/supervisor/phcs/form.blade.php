@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
        'marketing_admin'         => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-8 text-center">
        {{ isset($phc) ? '✏️ Edit Project Handover Checklist (PHC)' : '➕ Create Project Handover Checklist (PHC)' }}
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
        <a href="{{ route('supervisor.project.show', $project->pn_number ?? $phc->project_id) }}"
           class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            ⬅️ Back to Project
        </a>
    </div>

    <form action="{{ isset($phc) ? route('phc.update', $phc) : route('phc.store') }}" method="POST">
        @csrf
        @if(isset($phc)) @method('PUT') @endif
        <input type="hidden" name="project_id" value="{{ old('project_id', $project->pn_number ?? $phc->project_id) }}">

        {{-- AlpineJS Component --}}
        <div x-data="phcForm()" x-cloak>
            {{-- Step Indicator --}}
           <div class="mb-4 md:mb-6 text-center text-gray-600 font-medium text-sm md:text-base">
                <template x-if="step === 1"><span>🔹 <strong>Step 1 of 3:</strong> General Information</span></template>
                <template x-if="step === 2"><span>📋 <strong>Step 2 of 3:</strong> Handover Checklist</span></template>
                <template x-if="step === 3"><span>📄 <strong>Step 3 of 3:</strong> Document Preparation</span></template>
            </div>

            {{-- Step Tabs --}}
            <div class="flex flex-col md:flex-row justify-center mb-6 gap-3 md:space-x-4">
                <template x-for="i in [1,2,3]" :key="i">
                    <button type="button" @click="setStep(i)" 
                        :class="{
                            'bg-blue-600 text-white': step === i,
                            'bg-gray-200 text-gray-700': step !== i
                        }"
                        class="px-4 py-2 rounded-md font-medium transition w-full md:w-40 text-sm md:text-base">
                        <span x-text="i === 1 ? '1️⃣ Information' : (i === 2 ? '2️⃣ Checklist' : '3️⃣ Documents')"></span>
                    </button>
                </template>
            </div>

            {{-- STEP 1: General Information --}}
            <div x-show="step === 1" x-transition class="space-y-5 md:space-y-6">
                <x-input.text name="project_name" label="Project" disabled :value="$project->project_name ?? $phc->project->project_name" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Handover Date --}}
                    <div>
                        <label for="handover_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Handover Date
                        </label>
                        @php
                            $handoverDate = old('handover_date') 
                                ?? (isset($phc) && $phc->handover_date 
                                    ? \Illuminate\Support\Carbon::parse($phc->handover_date)->format('Y-m-d') 
                                    : '');
                        @endphp
                        <input 
                            type="date" 
                            name="handover_date" 
                            id="handover_date" 
                            value="{{ $handoverDate }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm 
                                focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition"
                        >
                    </div>

                    <x-input.text name="project_number" label="PN No" disabled :value="$project->project_number ?? $phc->project->project_number" />
                </div>

                {{-- Start & Finish --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        @php
                            $startDate = old('start_date') ?? (isset($phc) && $phc->start_date ? \Illuminate\Support\Carbon::parse($phc->start_date)->format('Y-m-d') : '');
                        @endphp
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                            class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="target_finish_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Target Finish Date
                        </label>
                        @php
                            $targetFinishDate = old('target_finish_date') 
                                ?? (isset($phc) && $phc->target_finish_date
                                    ? \Illuminate\Support\Carbon::parse($phc->target_finish_date)->format('Y-m-d')
                                    : '');
                        @endphp
                        <input 
                            type="date" 
                            name="target_finish_date" 
                            id="target_finish_date" 
                            value="{{ $targetFinishDate }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                                focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition"
                        >
                    </div>

                </div>

                {{-- Quotation Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input.text name="no_quotation" label="Quotation No" disabled :value="$phc->no_quotation ?? $project->quotation->no_quotation" />
                    <x-input.text name="quotation_date" label="Quotation Date" disabled :value="optional($project->quotation->quotation_date)->format('d M Y')" />
                </div>

                {{-- Client Info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-input.text name="client_pic_name" label="Client Name" :value="old('client_pic_name', $phc->client_pic_name ?? '')" />
                    <x-input.text name="client_mobile" label="Client Mobile" :value="old('client_mobile', $phc->client_mobile ?? '')" />
                    <x-input.text name="client_reps_office_address" label="Client Representative Office Address" :value="old('client_reps_office_address', $phc->client_reps_office_address ?? '')" />
                </div>

                {{-- Client Info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-input.text name="client_site_address" label="Client Site Address" :value="old('client_site_address', $phc->client_site_address ?? '')" />
                    <x-input.text name="client_site_representatives" label="Client Site Representative" :value="old('client_site_representatives', $phc->client_site_representatives ?? '')" />
                    <x-input.text name="site_phone_number" label="Site Phone Number" :value="old('site_phone_number', $phc->client_site_address ?? '')" />
                </div>

                {{-- Engineer --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- HO Engineer --}}
                    <div>
                        <label for="ho_engineering_id" class="block text-sm font-medium text-gray-700 mb-1">
                            HO Engineer
                        </label>
                        <select id="ho_engineering_id" name="ho_engineering_id" class="select2 w-full" wire:ignore>
                            <option value="">-- Select HO Engineer --</option>
                            <optgroup label="Engineer">
                                @foreach ($users->filter(fn($u) => 
                                    $u->role && in_array(strtolower($u->role->name), ['engineer', 'engineer_supervisor', 'project manager', 'project controller', 'engineering_admin'])
                                ) as $user)
                                    <option value="{{ $user->id }}" {{ old('ho_engineering_id', $phc->ho_engineering_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                        {{-- PIC Engineer --}}
                    <div>
                        <label for="pic_engineering_id" class="block text-sm font-medium text-gray-700 mb-1">
                            PIC Engineer
                        </label>
                        <select id="pic_engineering_id" name="pic_engineering_id" class="select2 w-full" wire:ignore>
                            <option value="">-- Select PIC Engineer --</option>
                            <optgroup label="Engineer">
                                @foreach ($users->filter(fn($u) => 
                                    $u->role && in_array(strtolower($u->role->name), ['engineer', 'engineer_supervisor', 'project manager'])
                                ) as $user)
                                    <option value="{{ $user->id }}" {{ old('pic_engineering_id', $phc->pic_engineering_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>

                {{-- Marketing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- HO Marketings --}}
                    <div>
                        <label for="ho_marketings_id" class="block text-sm font-medium text-gray-700 mb-1">
                            HO Marketing
                        </label>
                        <select id="ho_marketings_id" name="ho_marketings_id" class="select2 w-full" wire:ignore>
                            <option value="">-- Select HO Marketing --</option>
                            <optgroup label="Marketing">
                                @foreach ($users->filter(fn($u) => 
                                    $u->role && in_array(strtolower($u->role->name), ['supervisor marketing', 'marketing_admin', 'marketing_director', 'marketing_estimator', 'sales_supervisor'])
                                ) as $user)
                                    <option value="{{ $user->id }}" {{ old('ho_marketings_id', $phc->ho_marketings_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    {{-- PIC Marketing --}}
                    <div>
                        <label for="pic_marketing_id" class="block text-sm font-medium text-gray-700 mb-1">
                            PIC Marketing
                        </label>
                        <select id="pic_marketing_id" name="pic_marketing_id" class="select2 w-full" wire:ignore>
                            <option value="">-- Select PIC Marketing --</option>
                            <optgroup label="Marketing">
                                @foreach ($users->filter(fn($u) => 
                                    $u->role && in_array(strtolower($u->role->name), ['supervisor marketing', 'marketing_admin', 'marketing_director', 'marketing_estimator', 'sales_supervisor'])
                                ) as $user)
                                    <option value="{{ $user->id }}" {{ old('pic_marketing_id', $phc->pic_marketing_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('notes', $phc->notes ?? '') }}</textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="setStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">⏭️ Next: Checklist</button>
                </div>
            </div>

            {{-- STEP 2: Handover Checklist --}}
            <div x-show="step === 2" x-transition class="space-y-5 md:space-y-6">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800">📋 Step 2: Handover Checklist</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    @foreach([
                        'costing_by_marketing' => 'Costing by Marketing',
                        'boq' => 'Bill of Quantity (BOQ)',
                        'retention' => 'retention',
                        'warranty' => 'warranty',
                        'penalty' => 'penalty',
                    ] as $key => $label)
                        <div x-data="{ applicable: '{{ old($key, isset($phc) ? ($phc->$key ? 'A' : 'NA') : 'NA') }}' }"
                            class="p-4 border rounded-md bg-gray-50">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="{{ $key }}" value="A" x-model="applicable"
                                        class="text-blue-600 border-gray-300">
                                    <span class="ml-2 text-sm">Applicable</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="{{ $key }}" value="NA" x-model="applicable"
                                        class="text-blue-600 border-gray-300">
                                    <span class="ml-2 text-sm">Not Applicable</span>
                                </label>
                            </div>
                            @if (in_array($key, ['retention', 'warranty', 'penalty']))
                                <div x-show="applicable === 'A'" class="mt-3 space-y-2" x-transition>
                                    @php
                                        $detailField = $key == 'retention' ? 'retention' :
                                                    ($key == 'warranty' ? 'warranty' : 'penalty');
                                    @endphp
                                    <x-input.text name="{{ $detailField }}" label="{{ $label }} Detail"
                                                :value="old($detailField, $phc->$detailField ?? '')" />
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="flex flex-col md:flex-row justify-between pt-4 gap-3">
                    <button type="button" @click="setStep(1)"
                            class="px-5 py-2 border rounded text-sm md:text-base w-full md:w-auto">⬅️ Back</button>
                    <button type="button" @click="setStep(3)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm md:text-base w-full md:w-auto">⏭️ Next: Documents</button>
                </div>
            </div>

            {{-- STEP 3: Document Preparation --}}
            <div x-show="step === 3" x-transition class="space-y-5 md:space-y-6">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800">📄 Document Preparation</h3>

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
                    <div>
                        <h4 class="font-medium text-gray-700 mt-4 mb-2 text-sm md:text-base">{{ $groupTitle }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($fields as $key => $label)
                                @php $default = old($key, isset($phc) ? ($phc->$key ? 'A' : 'NA') : 'NA'); @endphp

                                @if ($key === 'scope_of_work_approval')
                                    <div x-data="{ sow: @js($default), openModal: false }" class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                        <div class="flex flex-wrap gap-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="A" x-model="sow">
                                                <span class="ml-2 text-sm">Applicable</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="NA" x-model="sow">
                                                <span class="ml-2 text-sm">N/A</span>
                                            </label>
                                        </div>

                                        {{-- SOW Modal Button --}}
                                        <template x-if="sow === 'A'">
                                            <div class="mt-2">
                                                <button type="button"
                                                        onclick="Livewire.dispatch('openModal')"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm w-full md:w-auto">
                                                    + Add SOW
                                                </button>
                                            </div>
                                        </template>

                                        {{-- SOW Modal --}}
                                        <div class="w-full">
                                            @livewire('supervisor-marketing.scope-of-work-form-modal', ['projectId' => $project->pn_number])
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                        <div class="flex flex-wrap gap-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="A" @checked($default === 'A')>
                                                <span class="ml-2 text-sm">Applicable</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="{{ $key }}" value="NA" @checked($default === 'NA')>
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
                            class="px-5 py-2 border rounded text-sm md:text-base w-full md:w-auto">⬅️ Back</button>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded text-sm md:text-base w-full md:w-auto">💾 Save PHC</button>
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
            minimumResultsForSearch: 5, // enable search if options > 5
        });
    });
</script>
@endpush

{{-- AlpineJS for step control --}}
<script>
function phcForm() {
    return {
        step: 1,
        setStep(i) { this.step = i; window.scrollTo({ top: 0, behavior: 'smooth' }); }
    }
}
</script>
@endsection