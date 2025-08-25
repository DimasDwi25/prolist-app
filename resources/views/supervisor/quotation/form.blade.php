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
<div class="max-w-5xl mx-auto p-8 bg-white rounded-xl shadow-lg">
 @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="mb-8 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ optional($quotation)->exists ? 'Edit Quotation Details' : 'Create New Quotation' }}
        </h1>
        <p class="text-gray-600 mt-1">
            {{ optional($quotation)->exists ? 'Update existing quotation information' : 'Fill in the form below to create a new quotation' }}
        </p>
    </div>

    <form action="{{ optional($quotation)->exists ? route('quotation.update', $quotation) : route('quotation.store') }}" method="POST">
        @csrf
        @if(optional($quotation)->exists) @method('PUT') @endif
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        <!-- Client Information Section -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                Client Information
            </h3>
            
            <div class="grid md:grid-cols-2 gap-4">
                <!-- Client Selection -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Client <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Select from existing clients)</span>
                    </label>
                    <select id="client_id" name="client_id" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Select Client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $quotation->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Client PIC -->
                <div>
                    <label for="client_pic" class="block text-sm font-medium text-gray-700 mb-1">
                        Point of Contact <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Person in charge)</span>
                    </label>
                    <input type="text" id="client_pic" name="client_pic" value="{{ old('client_pic', optional($quotation)->client_pic) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           required placeholder="e.g. John Doe (Marketing Manager)">
                    @error('client_pic')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Quotation Details Section -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H5a1 1 0 010-2h12a2 2 0 001-2V4a2 2 0 00-2-2H6a2 2 0 00-2 2z" clip-rule="evenodd" />
                </svg>
                Quotation Details
            </h3>

            <!-- Title -->
            <div class="mb-4">
                <label for="title_quotation" class="block text-sm font-medium text-gray-700 mb-1">
                    Project Title <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-500 ml-1">(Brief description of the project)</span>
                </label>
                <input type="text" id="title_quotation" name="title_quotation" value="{{ old('title_quotation', optional($quotation)->title_quotation) }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                       required placeholder="e.g. Electrical Installation for Office Tower">
                @error('title_quotation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dates and Numbers -->
            <div class="grid md:grid-cols-3 gap-4">
               <!-- Inquiry Date -->
                <div class="mb-4">
                    <label for="inquiry_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Inquiry Date
                        <span class="text-xs text-gray-500 ml-1">(When client first contacted)</span>
                    </label>
                    @if($quotation->exists)
                        <input type="text" 
                            value="{{ $quotation->inquiry_date?->format('Y-m-d') }}" 
                            class="w-full rounded-md bg-gray-100 border-gray-300" 
                            disabled>
                        <input type="hidden" name="inquiry_date" value="{{ $quotation->inquiry_date?->format('Y-m-d') }}">
                    @else
                        <input type="date" 
                            id="inquiry_date" 
                            name="inquiry_date"
                            value="{{ old('inquiry_date') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm"
                            required>
                    @endif
                    @error('inquiry_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quotation Date -->
                <div class="mb-4">
                    <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Quotation Date
                        <span class="text-xs text-gray-500 ml-1">(When quote was prepared)</span>
                    </label>
                    @if($quotation->exists)
                        <input type="text" 
                            value="{{ $quotation->quotation_date?->format('Y-m-d') }}" 
                            class="w-full rounded-md bg-gray-100 border-gray-300" 
                            disabled>
                        <input type="hidden" name="quotation_date" value="{{ $quotation->quotation_date?->format('Y-m-d') }}">
                    @else
                        <input type="date" 
                            id="quotation_date" 
                            name="quotation_date"
                            value="{{ old('quotation_date') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm"
                            required>
                    @endif
                    @error('quotation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Quotation Week (auto-generated) -->
                <div>
                    <label for="quotation_weeks" class="block text-sm font-medium text-gray-700 mb-1">
                        Quotation Week
                        <span class="text-xs text-gray-500 ml-1">(Auto-generated)</span>
                    </label>
                    <input type="text" id="quotation_weeks" name="quotation_weeks" 
                           value="{{ old('quotation_weeks', optional($quotation)->quotation_weeks) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                </div>
            </div>

            <!-- Quotation Number -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Quotation Number
                    <span class="text-xs text-gray-500 ml-1">(Auto-generated)</span>
                </label>
                <div class="flex items-center">
                    <span class="inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-gray-600 text-sm rounded-l-md">Q-</span>
                    <input type="text" 
                        name="no_quotation" 
                        value="{{ old('no_quotation', $quotation->exists ? substr($quotation->no_quotation, 2, 3) : ($noQuotationNumber ?? '')) }}" 
                        class="flex-1 min-w-0 block w-full px-3 py-2 border-t border-b border-gray-300 bg-white text-sm"
                        pattern="\d{1,3}"
                        maxlength="3">


                    <span class="inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-gray-600 text-sm rounded-r-md">
                        /<span id="display_month_roman" class="mx-1">{{ $monthRoman }}</span>/
                        <span id="display_year" class="ml-1">{{ optional($quotation->quotation_date ?? now())->format('y') }}</span>
                    </span>
                </div>
                <input type="hidden" name="month_roman" value="{{ $monthRoman }}">
                @if($quotation->exists)
                    <input type="hidden" name="quotation_number" value="{{ $quotation->quotation_number }}">
                @endif
            </div>
        </div>

        <!-- Financial Information Section -->
        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                </svg>
                Financial Information
            </h3>

            <div class="grid md:grid-cols-2 gap-4">
                <!-- Quotation Value -->
                <div>
                    <label for="quotation_value" class="block text-sm font-medium text-gray-700 mb-1">
                        Quotation Value (IDR) <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Total project value)</span>
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="text" id="quotation_value" name="quotation_value" 
                            value="{{ old('quotation_value', $quotation->quotation_value ?? '') }}"
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 py-2 border-gray-300 rounded-md" 
                            required placeholder="25.000.000">
                        <div class="absolute inset-y-0 right-0 flex items-center">
                        </div>
                    </div>
                    <!-- Hidden input to store the raw numeric value -->
                    <input type="hidden" id="quotation_value_raw" name="quotation_value_raw" 
                        value="{{ old('quotation_value_raw', optional($quotation)->quotation_value) }}">
                    @error('quotation_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Revision Information Section -->
        <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Revision Information
            </h3>

            <div class="grid md:grid-cols-2 gap-4">
                <!-- Revision Date -->
                <div>
                    <label for="revision_quotation_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Revision Date
                        <span class="text-xs text-gray-500 ml-1">(When last revised)</span>
                    </label>
                    <input 
                        type="date" 
                        id="revision_quotation_date" 
                        name="revision_quotation_date" 
                        value="{{ old('revision_quotation_date', optional($quotation?->revision_quotation_date)->format('Y-m-d')) }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm bg-white focus:ring-blue-500 focus:border-blue-500 @error('revision_quotation_date') border-red-500 @enderror"
                    >
                    @error('revision_quotation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Revision Number -->
                <div>
                    <label for="revisi" class="block text-sm font-medium text-gray-700 mb-1">
                        Revision Number
                        <span class="text-xs text-gray-500 ml-1">(e.g. Rev.1, Rev.2)</span>
                    </label>
                    <input type="text" id="revisi" name="revisi" 
                           value="{{ old('revisi', optional($quotation)->revisi) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="e.g. Rev.1">
                    @error('revisi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4 pt-6">
            <a href="{{ route('quotation.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ isset($quotation) && $quotation->exists ? 'Update Quotation' : 'Create Quotation' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5"></script>


<script>
    $(document).ready(function() {
        // Initialize Select2 for client selection
        $('#client_id').select2({
            placeholder: "Search for a client...",
            allowClear: true,
            width: '100%'
        });

        @if(isset($quotation) && $quotation->client)
            // Pre-select the client if editing
            var clientOption = new Option("{{ $quotation->client->name }}", "{{ $quotation->client->id }}", true, true);
            $('#client_id').append(clientOption).trigger('change');
        @endif

        // Auto-generate week number when date changes
        $('input[name="quotation_date"]').on('change', function() {
            if(this.value) {
                const date = new Date(this.value);
                const weekNumber = getWeekOfYear(date);
                $('input[name="quotation_weeks"]').val(`${date.getFullYear()}-W${weekNumber}`);
                
                // Update Roman numeral month display
                const month = date.getMonth() + 1;
                $('#display_month_roman').text(convertToRoman(month));
                $('#month_roman').val(convertToRoman(month));
                
                // Update year display
                $('#display_year').text(date.getFullYear().toString().slice(-2));
            }
        });

        // Helper function to get week number
        function getWeekOfYear(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        // Helper function to convert to Roman numerals
        function convertToRoman(num) {
            const roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            return roman[num - 1] || '';
        }

        // Inisialisasi AutoNumeric pada quotation_value
        const quotationValueAutoNum = new AutoNumeric('#quotation_value', {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            currencySymbol: 'Rp ',
            currencySymbolPlacement: 'p', // prefix
            unformatOnSubmit: true // biar otomatis kirim angka mentah kalau submit form
        });

        // Update hidden field setiap kali value berubah
        $('#quotation_value').on('keyup change', function () {
            $('#quotation_value_raw').val(quotationValueAutoNum.getNumber()); 
        });    
    });
</script>

<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        padding-left: 12px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    
    .select2-container--default .select2-results__option--highlighted {
        background-color: #3b82f6;
    }
    
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush