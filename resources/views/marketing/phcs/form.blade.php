@extends('marketing.layouts.app')

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
        <a href="{{ route('project.show', $project->id ?? $phc->project_id) }}"
        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            ⬅️ Back to Project
        </a>
    </div>


    <form action="{{ isset($phc) ? route('phc.update', $phc) : route('phc.store') }}" method="POST">
        @csrf
        @if(isset($phc)) @method('PUT') @endif
        <input type="hidden" name="project_id" value="{{ old('project_id', $project->id ?? $phc->project_id) }}">

       <div x-data="phcForm()" x-init="init()" x-cloak>
            {{-- Step Indicator --}}
            <div class="mb-6 text-center text-gray-600 font-medium">
                <template x-if="step === 1"><span>🔹 <strong>Step 1 of 3:</strong> Informasi Umum</span></template>
                <template x-if="step === 2"><span>📋 <strong>Step 2 of 3:</strong> Handover Checklist</span></template>
                <template x-if="step === 3"><span>📄 <strong>Step 3 of 3:</strong> Document Preparation</span></template>
            </div>

            {{-- Step Tabs --}}
            <div class="flex justify-center mb-6 space-x-4">
                <template x-for="i in 3">
                    <button type="button" @click="step = i" :class="{
                        'bg-blue-600 text-white': step === i,
                        'bg-gray-200 text-gray-700': step !== i
                    }" class="px-4 py-2 rounded-md font-medium transition w-40">
                        <template x-if="i === 1">1️⃣ Informasi</template>
                        <template x-if="i === 2">2️⃣ Checklist</template>
                        <template x-if="i === 3">3️⃣ Dokumen</template>
                    </button>
                </template>
            </div>

            {{-- STEP 1 --}}
            <div x-show="step === 1" class="space-y-6">
                <x-input.text name="project_name" label="Project" disabled :value="$project->project_name ?? $phc->project->project_name" />

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="handover_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Handover Date
                        </label>
                        
                       @php
                            $handoverDate = old('handover_date') 
                            ?? (isset($phc) && $phc->handover_date ? \Illuminate\Support\Carbon::parse($phc->handover_date)->format('Y-m-d') : '');
                        @endphp


                        <input type="date" name="handover_date" id="handover_date"
                            value="{{ $handoverDate }}"
                            class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm px-3 py-2">
                    </div>
                    <x-input.text name="project_number" label="PN No" disabled :value="$project->project_number ?? $phc->project->project_number" />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Start Date
                        </label>
                        
                        @php
                            $startDate = old('start_date') 
                            ?? (isset($phc) && $phc->start_date ? \Illuminate\Support\Carbon::parse($phc->start_date)->format('Y-m-d') : '');
                        @endphp

                        <input type="date" name="start_date" id="start_date"
                            value="{{ $startDate }}"
                            class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm px-3 py-2">
                    </div>


                    {{-- Target Finish Date --}}
                    <div>
                        <label for="target_finish_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Target Finish Date
                        </label>
                        
                        @php
                            $targetFinishDate = old('target_finish_date') 
                            ?? (isset($phc) && $phc->target_finish_date ? \Illuminate\Support\Carbon::parse($phc->target_finish_date)->format('Y-m-d') : '');
                        @endphp

                        <input type="date" name="target_finish_date" id="target_finish_date"
                            value="{{ $targetFinishDate }}"
                            class="w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm px-3 py-2">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <x-input.text name="no_quotation" label="No Quotation" disabled :value="$phc->no_quotation ?? $project->quotation->no_quotation" />
                    <x-input.text name="quotation_date" label="Quotation Date" disabled :value="optional($project->quotation->quotation_date)->format('d M Y')" />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <x-input.text name="po_no" label="PO No." disabled :value="$project->quotation->po_number ?? ''" />
                    <x-input.text name="po_date" label="PO Date" disabled :value="optional($project->quotation->po_date)->format('d M Y')" />
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <x-input.text name="client_name" label="Client Name" :value="old('client_name', $phc->client_name ?? '')" />
                    <x-input.text name="client_mobile" label="Client Mobile" :value="old('client_mobile', $phc->client_mobile ?? '')" />
                    <x-input.text name="client_reps_office_address" label="Client Reps Office Address" :value="old('client_reps_office_address', $phc->client_reps_office_address ?? '')" />
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <x-input.text name="client_site_representatives" label="Office Representative" :value="old('client_site_representatives', $phc->client_site_representatives ?? '')" />
                    <x-input.text name="client_site_address" label="Site Address" :value="old('client_site_address', $phc->client_site_address ?? '')" />
                    <x-input.text name="site_phone_number" label="Site Phone" :value="old('site_phone_number', $phc->site_phone_number ?? '')" />
                </div>

                {{-- HO Engineer dan PIC Engineer --}}
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="ho_engineering_id" class="text-sm text-gray-600">HO Engineer</label>
                        <select name="ho_engineering_id" id="ho_engineering_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Engineer --</option>
                            @foreach($users->where('role.name', 'engineer') as $u)
                                <option value="{{ $u->id }}" @selected(old('ho_engineering_id', $phc->ho_engineering_id ?? '') == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="pic_engineering_id" class="text-sm text-gray-600">PIC Engineer</label>
                        <select name="pic_engineering_id" id="pic_engineering_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Engineer --</option>
                            @foreach($users->where('role.name', 'engineer') as $u)
                                <option value="{{ $u->id }}" @selected(old('pic_engineering_id', $phc->pic_engineering_id ?? '') == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- HO Marketing dan PIC Marketing --}}
                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="ho_marketings_id" class="text-sm text-gray-600">HO Marketing</label>
                        <select name="ho_marketings_id" id="ho_marketing_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Marketing --</option>
                            @foreach($users->where('role.name', 'marketing') as $u)
                                <option value="{{ $u->id }}" @selected(old('ho_marketings_id', $phc->ho_marketings_id ?? '') == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="pic_marketing_id" class="text-sm text-gray-600">PIC Marketing</label>
                        <select name="pic_marketing_id" id="pic_marketing_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Marketing --</option>
                            @foreach($users->where('role.name', 'marketing') as $u)
                                <option value="{{ $u->id }}" @selected(old('pic_marketing_id', $phc->pic_marketing_id ?? '') == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div>
                    <label class="text-sm text-gray-600">Notes</label>
                    <textarea name="notes" class="w-full border rounded px-3 py-2">{{ old('notes', $phc->notes ?? '') }}</textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="setStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">⏭️ Next: Checklist</button>
                </div>
            </div>

            {{-- STEP 2 --}}
            <div x-show="step === 2" class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800">📋 Step 2: Handover Checklist</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                        'costing_by_marketing' => 'Costing by Marketing',
                        'boq' => 'Bill of Quantity (BOQ)',
                        'retention_applicability' => 'Retention Applicability',
                        'warranty' => 'Warranty',
                        'penalty' => 'Penalty',
                    ] as $key => $label)
                        <div x-data="{ applicable: '{{ old($key, isset($phc) ? ($phc->$key ? 'A' : 'NA') : 'NA') }}' }" class="p-4 border rounded-md bg-gray-50">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
                            <div class="flex items-center gap-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="{{ $key }}" value="A" x-model="applicable" class="text-blue-600 border-gray-300">
                                    <span class="ml-2">Applicable</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="{{ $key }}" value="NA" x-model="applicable" class="text-blue-600 border-gray-300">
                                    <span class="ml-2">Not Applicable</span>
                                </label>
                            </div>

                            @if (in_array($key, ['retention_applicability', 'warranty', 'penalty']))
                                <div x-show="applicable === 'A'" class="mt-3" x-transition>
                                    @php
                                        $detailField = $key == 'retention_applicability' ? 'retention' : ($key == 'warranty' ? 'warranty_detail' : 'penalty_detail');
                                    @endphp
                                    <x-input.text 
                                        name="{{ $detailField }}" 
                                        label="{{ $label }} Detail"
                                        :value="old($detailField, $phc->$detailField ?? '')" />
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between pt-6">
                    <button type="button" @click="setStep(1)" class="px-5 py-2 border rounded">⬅️ Back</button>
                    <button type="button" @click="setStep(3)" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">⏭️ Next: Dokumen</button>
                </div>
            </div>


            {{-- STEP 3 --}}
            <div x-show="step === 3" class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800">📄 Document Preparation</h3>

                @foreach([
                    '📐 Desain & Gambar' => [
                        'scope_of_work_approval' => 'Scope of Work Approval',
                        'design_approval_draw' => 'Design Approval Drawings',
                        'shop_draw' => 'Shop Drawing',
                        'as_build_draw' => 'As-Built Drawings',
                    ],
                    '📋 Laporan & Form' => [
                        'project_schedule' => 'Project Schedule',
                        'progress_claim_report' => 'Progress Claim Report',
                        'fat_sat_forms' => 'FAT/SAT Forms',
                        'daily_weekly_progress_report' => 'Daily/Weekly Progress Report',
                        'site_testing_commissioning_report' => 'Testing & Commissioning Report',
                        'accomplishment_report' => 'Accomplishment Report',
                    ],
                    '🧰 Dokumen Penunjang' => [
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
                        <h4 class="font-medium text-gray-700 mt-6 mb-2">{{ $groupTitle }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($fields as $key => $label)
                                @php
                                    $default = old($key, isset($phc) ? ($phc->$key ? 'A' : 'NA') : 'NA');
                                @endphp
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                                    <div class="flex gap-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="{{ $key }}" value="A" @checked($default === 'A')>
                                            <span class="ml-2">Applicable</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="{{ $key }}" value="NA" @checked($default === 'NA')>
                                            <span class="ml-2">N/A</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between pt-6">
                    <button type="button" @click="setStep(2)" class="px-5 py-2 border rounded">⬅️ Back</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded">
                        {{ isset($phc) ? '💾 Update PHC' : '✅ Create PHC' }}
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function phcForm() {
    return {
        step: 1,
        setStep(val) {
            this.step = val;
        }
    }
}
</script>
@if(isset($phc))
<script>
    localStorage.removeItem('phcStep');
</script>
@endif

@endpush

