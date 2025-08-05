@extends('supervisor.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">
        {{ isset($quotation) ? 'Edit Quotation' : 'Create Quotation' }}
    </h2>

    <form action="{{ isset($quotation) ? route('quotation.update', $quotation) : route('quotation.store') }}" method="POST">
        @csrf
        @if(isset($quotation)) @method('PUT') @endif

        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        {{-- Client & Inquiry --}}
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            {{-- Client --}}
            <div class="mb-4">
                <label for="client_id" class="block text-sm font-semibold text-gray-800 mb-1">Client</label>
                
                <select id="client_id" name="client_id"
                    class="js-example-basic-single w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm shadow-sm">
                    <option value="">-- Select Client --</option>
                </select>


                @error('client_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Client PIC --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Client PIC</label>
                <input type="text" name="client_pic" value="{{ old('client_pic', $quotation->client_pic ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
                @error('client_pic') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- Inquiry Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Inquiry Date</label>
                <input type="date" name="inquiry_date"
                    value="{{ old('inquiry_date', isset($quotation->inquiry_date) ? optional($quotation->inquiry_date)->format('Y-m-d') : '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1">

                @error('inquiry_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Quotation Title --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Quotation Title</label>
            <input type="text" name="title_quotation" value="{{ old('title_quotation', $quotation->title_quotation ?? '') }}">

            @error('title_quotation') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        {{-- Quotation Date & Number --}}
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            {{-- Quotation Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Quotation Date</label>
                <input type="date" name="quotation_date"
                       value="{{ old('quotation_date', isset($quotation->quotation_date) ? $quotation->quotation_date->format('Y-m-d') : '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1"
                       >
                @error('quotation_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Quotation Number</label>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="px-2 py-2 border rounded bg-gray-100">Q-</span>
                    <input type="text" name="no_quotation" value="{{ old('no_quotation', $noQuotationNumber) }}"
                        class="border rounded px-3 py-2 w-24" required>
                    <span class="px-2 py-2 border rounded bg-gray-100">/{{ \App\Models\Quotation::getCurrentMonthRoman() }}/{{ now()->format('y') }}</span>
                </div>
            </div>

            {{-- Quotation Week --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Quotation Week</label>
                <input type="text" name="quotation_weeks" value="{{ old('quotation_weeks', $quotation->quotation_weeks ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2 mt-1 bg-gray-100" readonly>
            </div>
        </div>

        {{-- Financial Info --}}
        <div class="grid md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Quotation Value</label>
                <input type="number" name="quotation_value" value="{{ old('quotation_value', $quotation->quotation_value ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Revision Date</label>
                <input type="date" name="revision_quotation_date"
                       value="{{ old('revision_quotation_date', isset($quotation) ? optional($quotation->revision_quotation_date)->format('Y-m-d') : '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Revisi</label>
                <input type="text" name="revisi" value="{{ old('revisi', $quotation->revisi ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
            </div>

            @if(isset($quotation))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                        <option value="">-- Choose --</option>
                        @foreach ([
                            'A' => '(A) Quotation and PO Completed',
                            'D' => '(D) Project belum ada PO',
                            'E' => '(E) Penawaran Project Batal',
                            'F' => '(F) Penawaran Project Kalah',
                            'O' => '(O) On Going'
                        ] as $key => $val)
                            <option value="{{ $key }}" {{ old('status', $quotation->status ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                {{-- Saat create, set value default 'O' tanpa form input --}}
                <input type="hidden" name="status" value="O">
            @endif

        </div>
        @if(isset($quotation))
            {{-- PO Info --}}
            <div id="po-info-section" class="border-t pt-6 mt-6 hidden">
                <h3 class="text-lg font-semibold mb-4">PO Information</h3>
                <div class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PO Date</label>
                        <input type="date" name="po_date" value="{{ old('po_date', optional($quotation->po_date ?? null)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sales Week</label>
                        <input type="text" name="sales_weeks" value="{{ old('sales_weeks', $quotation->sales_weeks ?? '') }}"
                            class="w-full border border-gray-300 rounded px-3 py-2 mt-1 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">PO Number</label>
                        <input type="text" name="po_number" value="{{ old('po_number', $quotation->po_number ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PO Value</label>
                        <input type="number" name="po_value" value="{{ old('po_value', $quotation->po_value ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project Value</label>
                        <input type="number" name="project_value" value="{{ old('project_value', $quotation->project_value ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    </div>
                </div>
            </div>
        @endif


        {{-- Action Buttons --}}
        <div class="flex justify-end mt-8">
            <a href="{{ route('quotation.index') }}" class="text-gray-600 hover:underline mr-6">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                {{ isset($quotation) ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<!-- jQuery and Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 with better configuration
        $(document).ready(function () {
            console.log("Select2 Init Running"); // cek di console

            $('#client_id').select2({
                placeholder: "-- Search Client --",
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('ajax.clients') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        console.log("Kirim query:", params.term); // log query
                        return { q: params.term || '' };
                    },
                    processResults: function (data) {
                        console.log("Data dari server:", data); // log hasil
                        return { results: data };
                    },
                    cache: true
                }
            });

            @if(isset($quotation) && $quotation->client)
                var clientOption = new Option("{{ $quotation->client->name }}", "{{ $quotation->client->id }}", true, true);
                $('#client_id').append(clientOption).trigger('change');
            @endif
        });



        // Rest of your existing JavaScript...
        function getWeek(date) {
            const d = new Date(date);
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return `${d.getUTCFullYear()}-W${weekNo.toString().padStart(2, '0')}`;
        }

        const quotationDateInput = document.querySelector('input[name="quotation_date"]');
        const quotationWeekInput = document.querySelector('input[name="quotation_weeks"]');
        const poDateInput = document.querySelector('input[name="po_date"]');
        const salesWeekInput = document.querySelector('input[name="sales_weeks"]');
        const statusSelect = document.querySelector('#status');
        const poInfoSection = document.querySelector('#po-info-section');

        function togglePOSection(status) {
            if (status === 'A' || status === 'D') {
                poInfoSection.classList.remove('hidden');
            } else {
                poInfoSection.classList.add('hidden');
            }
        }

        if (quotationDateInput && quotationWeekInput) {
            quotationDateInput.addEventListener('change', function () {
                quotationWeekInput.value = this.value ? getWeek(this.value) : '';
            });
        }

        if (poDateInput && salesWeekInput) {
            poDateInput.addEventListener('change', function () {
                salesWeekInput.value = this.value ? getWeek(this.value) : '';
            });
        }

        if (statusSelect && poInfoSection) {
            // On page load
            togglePOSection(statusSelect.value);

            // On change
            statusSelect.addEventListener('change', function () {
                togglePOSection(this.value);
            });
        }
    });
</script>
<style>
    .select2-container--default .select2-selection--single {
        height: 44px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.95rem;
        background-color: #fff;
        transition: border-color 0.2s;
    }

    .select2-container--default .select2-selection--single:hover {
        border-color: #2563eb;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111827;
        line-height: 28px;
        padding-left: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 10px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb;
        color: white;
    }

    .select2-container--default .select2-results__option {
        padding: 10px 16px;
        font-size: 0.9rem;
    }

    .select2-dropdown {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .select2-search--dropdown .select2-search__field {
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        font-size: 0.9rem;
    }
</style>

@endpush


