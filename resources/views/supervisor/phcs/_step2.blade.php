{{-- STEP 2: Handover Checklist --}}
<div class="space-y-5 md:space-y-6">
    <h3 class="text-lg md:text-xl font-semibold text-gray-800">📋 Step 2: Handover Checklist</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        @foreach([
            'costing_by_marketing' => 'Costing by Marketing',
            'boq' => 'Bill of Quantity (BOQ)',
            'retention' => 'Retention',
            'warranty' => 'Warranty',
            'penalty' => 'Penalty',
        ] as $key => $label)
            <div x-data="{ applicable: '{{ old($key, isset($phc) ? ($phc->$key ? 'A' : 'NA') : 'NA') }}' }"
                 class="p-4 border rounded-md bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>

                {{-- Pilihan Applicable / Not Applicable --}}
                <div class="flex flex-wrap items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="{{ $key }}" value="A" x-model="applicable"
                               class="text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm">Applicable</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="{{ $key }}" value="NA" x-model="applicable"
                               class="text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm">Not Applicable</span>
                    </label>
                </div>

                {{-- Detail tambahan untuk Retention, Warranty, Penalty --}}
                @if (in_array($key, ['retention', 'warranty', 'penalty']))
                    <div x-show="applicable === 'A'" class="mt-3 space-y-2" x-transition>
                        <label for="{{ $key }}_detail" class="block text-sm text-gray-600">
                            {{ $label }} Detail
                        </label>
                        <input type="text"
                               name="{{ $key }}"
                               id="{{ $key }}_detail"
                               value="{{ old($key, $phc->$key ?? '') }}"
                               class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-200 text-sm">
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Navigasi Step --}}
    <div class="flex flex-col md:flex-row justify-between pt-4 gap-3">
        <button type="button" @click="setStep(1)"
                class="px-5 py-2 border rounded text-sm md:text-base w-full md:w-auto">⬅️ Back</button>

        @if($isMarketing)
            {{-- Jika role Marketing langsung bisa Save --}}
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded text-sm md:text-base w-full md:w-auto">
                💾 Save PHC
            </button>
        @else
            {{-- Kalau bukan Marketing, lanjut ke Step 3 --}}
            <button type="button" @click="setStep(3)"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm md:text-base w-full md:w-auto">
                ⏭️ Next: Documents
            </button>
        @endif
    </div>
</div>
