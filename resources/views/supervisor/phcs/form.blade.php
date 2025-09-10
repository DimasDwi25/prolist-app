@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
        'marketing_admin'         => 'supervisor.layouts.app',
        'engineering_director'  => 'engineering_director.layouts.app',
        'marketing_estimator' => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';

    $marketingRoles = [
        'marketing_director',
        'supervisor marketing',
        'manager_marketing',
        'sales_supervisor',
        'marketing_admin',
        'marketing_estimator',
    ];

    // ✅ Tambahkan ini
     $isMarketing = in_array(Auth::user()->role->name, $marketingRoles);
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
                <button type="button" @click="setStep(1)"
                    :class="{
                        'bg-blue-600 text-white': step === 1,
                        'bg-gray-200 text-gray-700': step !== 1
                    }"
                    class="px-4 py-2 rounded-md font-medium transition w-full md:w-40 text-sm md:text-base">
                    1️⃣ Information
                </button>

                <button type="button" @click="setStep(2)"
                    :class="{
                        'bg-blue-600 text-white': step === 2,
                        'bg-gray-200 text-gray-700': step !== 2
                    }"
                    class="px-4 py-2 rounded-md font-medium transition w-full md:w-40 text-sm md:text-base">
                    2️⃣ Checklist
                </button>

                @if($isMarketing)
                    {{-- Step 3 disabled untuk marketing --}}
                    <button type="button" disabled
                        class="px-4 py-2 rounded-md w-full md:w-40 text-sm md:text-base 
                            bg-gray-100 text-gray-400 cursor-not-allowed relative group">
                        3️⃣ Documents
                    </button>
                @else
                    {{-- Step 3 aktif untuk non-marketing --}}
                    <button type="button" @click="setStep(3)"
                        :class="{
                            'bg-blue-600 text-white': step === 3,
                            'bg-gray-200 text-gray-700': step !== 3
                        }"
                        class="px-4 py-2 rounded-md font-medium transition w-full md:w-40 text-sm md:text-base">
                        3️⃣ Documents
                    </button>
                @endif
            </div>


            {{-- STEP 1: General Information --}}
            <div x-show="step === 1" x-transition>
                {{-- isi Step 1 --}}
                @include('supervisor.phcs._step1')
            </div>

            {{-- STEP 2 --}}
            <div x-show="step === 2" x-transition>
                {{-- isi Step 2 --}}
               @include('supervisor.phcs._step2')
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