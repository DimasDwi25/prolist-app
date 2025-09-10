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
                    focus:border-blue-500 focus:ring-blue-200 focus:ring-opacity-50 transition"
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
