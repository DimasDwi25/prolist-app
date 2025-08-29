@php
    $roleLayouts = [
        'project controller'     => 'project-controller.layouts.app',
        'engineer'     => 'engineer.layouts.app',
        'engineering_manager'         => 'project-manager.layouts.app',
        'engineering_director'  => 'engineering_director.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
    @php $isEdit = isset($workOrder); @endphp

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 px-4 py-3 rounded-md mb-6">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-sm font-medium">There were {{ $errors->count() }} error(s) with your submission</h3>
            </div>
            <div class="mt-2 text-sm pl-7">
                <ul class="list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="flex-1 max-w-6xl mx-auto bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-6 4h2m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $isEdit ? 'Edit Work Order' : 'Create Work Order' }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $isEdit ? 'Update your work order details below.' : 'Fill in details to create a new work order.' }}
                </p>
            </div>
            <a href="{{ route('engineer.work_order') }}" class="text-sm text-gray-600 hover:text-blue-600 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ $isEdit ? route('engineer.work-orders.update', $workOrder) : route('engineer.work-orders.store') }}" class="space-y-8">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- CLIENT & PROJECT --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                    <select name="client_id" id="client_id" class="select2 w-full rounded-md border-gray-300 focus:ring-blue-500">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $workOrder->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project (PN)</label>
                    <select name="project_id" id="project_id" class="select2 w-full rounded-md border-gray-300 focus:ring-blue-500">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->pn_number }}" {{ old('project_id', $workOrder->project_id ?? '') == $project->pn_number ? 'selected' : '' }}>
                                {{ $project->project_number }} - {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Work Order Details Section -->
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WO Date</label>
                    <input type="date" name="wo_date" value="{{ old('wo_date', $workOrder->wo_date ?? '') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WO Number</label>
                    <div class="flex rounded-md shadow-sm">
                        <span id="wo_kode_prefix"
                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-mono">
                            WO-00/000/
                        </span>
                        <input type="number" id="wo_number_last" name="wo_number_last" min="1"
                            value="{{ old('wo_number_last') ?? (isset($workOrder) ? explode('/', $workOrder->wo_kode_no)[2] : '') }}"
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <input type="hidden" name="wo_kode_no" id="wo_kode_no_real"
                value="{{ old('wo_kode_no', $workOrder->wo_kode_no ?? '') }}">

            {{-- DURATION --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Days)</label>
                <input type="number" min="1" name="wo_duration" value="{{ old('wo_duration', 1) }}" class="w-full rounded-md border-gray-300 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">WO will be created for multiple dates if >1</p>
            </div>

            {{-- START & END TIME --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Working Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $workOrder->start_time ?? '') }}" class="w-full rounded-md border-gray-300 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Working Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $workOrder->end_time ?? '') }}" class="w-full rounded-md border-gray-300 focus:ring-blue-500">
                </div>
            </div>

            {{-- DESCRIPTION & RESULT (Dynamic Rows) --}}
            <div class="space-y-4" id="work-rows-wrapper">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 work-row relative">
                    <button type="button" 
                        class="remove-row absolute top-2 right-2 text-gray-400 hover:text-red-600"
                        title="Remove this row">
                        ✕
                    </button>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Task 1</h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Work Description</label>
                            <textarea name="description[]" rows="3" 
                                class="w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Describe the work to be done...">{{ old('description.0', $workOrder->description ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Result</label>
                            <textarea name="result[]" rows="3" 
                                class="w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Describe the result after work...">{{ old('result.0', $workOrder->result ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Row Button -->
            <div class="flex justify-end mt-3">
                <button type="button" id="add-work-row" 
                    class="flex items-center gap-2 px-4 py-2 text-sm bg-green-600 text-white rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Task
                </button>
            </div>



            <!-- PIC Section -->
            <div class="space-y-4">
                <h3 class="font-medium text-gray-900">PIC (Person In Charge)</h3>
                <p class="text-gray-500 text-xs">Assign up to 5 members. Select both Member and their Role (type_role = 2).</p>

                <div class="grid md:grid-cols-2 gap-3">
                    @foreach(range(1,5) as $i)
                        <div class="flex items-center gap-2">
                            <!-- Label PIC -->
                            <span class="w-16 text-sm font-medium whitespace-nowrap">PIC {{ $i }}</span>

                            <!-- Member select -->
                            <select name="pic{{ $i }}" class="select2 flex-1 rounded border-gray-300 text-sm" data-placeholder="Select Member">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('pic'.$i, $workOrder->{'pic'.$i} ?? '') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Role select -->
                            <select name="role_pic_{{ $i }}" class="select2 flex-1 rounded border-gray-300 text-sm pic-role-select" data-placeholder="Select Role">
                                <option value=""></option>
                                @foreach ($roles->where('type_role', 2) as $role)
                                    <option value="{{ $role->id }}" data-role-name="{{ strtolower($role->name) }}" @selected(old('role_pic_'.$i, $workOrder->{'role_pic_'.$i} ?? '') == $role->id)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>


            {{-- SUBMIT --}}
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    {{ $isEdit ? 'Update Work Order' : 'Create Work Order' }}
                </button>
            </div>
        </form>
    </div>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form elements
    const projectSelect = document.getElementById('project_id');
    const clientInput = document.getElementById('client_name');
    const woKodePrefix = document.getElementById('wo_kode_prefix');
    const woNumberLast = document.getElementById('wo_number_last');
    const woKodeReal = document.getElementById('wo_kode_no_real');
    const projects = @json($projects);
    const projectWorkOrderCounts = @json($projectWorkOrderCounts);
    const year = new Date().getFullYear().toString().slice(-2);
    
    // Modal elements
    const submitButton = document.getElementById('submit-button');
    const logModal = document.getElementById('log-modal');
    const closeModal = document.getElementById('close-modal');
    const cancelModal = document.getElementById('cancel-modal');

    const wrapper = document.getElementById('work-rows-wrapper');
    const addBtn = document.getElementById('add-work-row');

    // Inisialisasi semua select2
    $('.select2').select2({
        theme: "classic", // bisa "bootstrap-5" kalau pakai bootstrap theme
        width: '100%',
        placeholder: 'Pilih opsi',
        allowClear: true
    });

    // Tambah Row Baru
    addBtn.addEventListener('click', () => {
        const rowCount = wrapper.querySelectorAll('.work-row').length + 1;

        const newRow = document.createElement('div');
        newRow.classList.add('bg-gray-50','border','border-gray-200','rounded-lg','p-4','work-row','relative','mt-3');
        newRow.innerHTML = `
            <button type="button" class="remove-row absolute top-2 right-2 text-gray-400 hover:text-red-600" title="Remove this row">✕</button>
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Task ${rowCount}</h4>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Work Description</label>
                    <textarea name="description[]" rows="3" class="w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Describe the work to be done..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Result</label>
                    <textarea name="result[]" rows="3" class="w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Describe the result after work..."></textarea>
                </div>
            </div>
        `;

        wrapper.appendChild(newRow);
        updateTaskTitles();
    });

    // Hapus Row
    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.work-row').remove();
            updateTaskTitles();
        }
    });

    // Update Judul Task (Task 1, Task 2...)
    function updateTaskTitles() {
        wrapper.querySelectorAll('.work-row').forEach((row, index) => {
            const title = row.querySelector('h4');
            if (title) title.textContent = `Task ${index + 1}`;
        });
    }


    // Update project prefix and client when project changes
    function updateProjectDetails() {
        const pid = projectSelect.value;
        const project = projects.find(p => p.pn_number == pid);
        
        // Update WO prefix
        if (project) {
            // Extract the last 3 digits from project_number (PN-25/001 atau CO-PN-25/001)
            const match = project.project_number.match(/^(?:CO-)?PN-\d{2}\/(\d{3})$/);
            const code = match ? match[1] : '000';

            
            // Get current year's last 2 digits
            const currentYear = new Date().getFullYear().toString().slice(-2);
            
            // Set WO prefix format: WO-25/001/
            woKodePrefix.textContent = `WO-${currentYear}/${code}/`;
            
            // Set default WO number if not manually edited
            if (!woNumberLast.dataset.edited || woNumberLast.value === '') {
                // Get count of existing WOs for this project and add 1
                woNumberLast.value = parseInt(projectWorkOrderCounts[pid] ?? 0) + 1;
            }
        } else {
            // Default format when no project selected
            const currentYear = new Date().getFullYear().toString().slice(-2);
            woKodePrefix.textContent = `WO-${currentYear}/000/`;
            woNumberLast.value = 1;
        }
        
        updateHiddenKode();
        fetchClient();
    }

    // Event untuk select2 project
    $('#project_id').on('select2:select', function () {
        updateProjectDetails();
    });

    // Event untuk select2 role PIC
    $(document).on('select2:select', '.pic-role-select', function () {
        updateMandays();
    });


    // Update the hidden WO code field
    function updateHiddenKode() {
        woKodeReal.value = woKodePrefix.textContent + (woNumberLast.value || '1');
    }

    // Fetch client name for selected project
    function fetchClient() {
        if (!projectSelect.value) {
            clientInput.value = '';
            return;
        }
        
        fetch(`/projects/${projectSelect.value}/client`)
            .then(res => res.json())
            .then(data => {
                clientInput.value = data.client_name || '';
            })
            .catch(() => {
                clientInput.value = '';
            });
    }

    // Calculate mandays based on selected roles
    function updateMandays() {
        let totalEng = 0, totalElect = 0;

        document.querySelectorAll('.pic-role-select').forEach(select => {
            const roleId = select.value;
            const option = select.querySelector(`option[value="${roleId}"]`);
            const roleName = option?.dataset.roleName?.toLowerCase();

            if (roleName === 'engineer') totalEng++;
            else if (roleName === 'electrician') totalElect++;
        });

        document.querySelector('input[name="total_mandays_eng"]').value = totalEng;
        document.querySelector('input[name="total_mandays_elect"]').value = totalElect;
    }


    // Validate log fields before submission
    function validateLogFields() {
        const category = document.getElementById('categorie_log_id');
        if (!category.value) {
            alert('Please select a log category');
            return false;
        }
        return true;
    }

    // Event Listeners
    projectSelect.addEventListener('change', updateProjectDetails);
    
    woNumberLast.addEventListener('input', function() {
        this.dataset.edited = true;
        updateHiddenKode();
    });
    
    document.querySelectorAll('.pic-role-select').forEach(select => {
        select.addEventListener('change', updateMandays);
    });
    
    // Modal handling
    submitButton.addEventListener('click', function() {
        logModal.classList.remove('hidden');
    });
    
    closeModal.addEventListener('click', function() {
        logModal.classList.add('hidden');
    });
    
    cancelModal.addEventListener('click', function() {
        logModal.classList.add('hidden');
    });
    
    document.querySelector('button[name="save_log"][value="yes"]').addEventListener('click', function(e) {
        if (!validateLogFields()) {
            e.preventDefault();
        }
    });

    // Initialize form on load
    woNumberLast.dataset.edited = false;
    // Event Listeners
    projectSelect.addEventListener('change', updateProjectDetails);
    $('#project_id').on('select2:select', updateProjectDetails);

    woNumberLast.addEventListener('input', function() {
        this.dataset.edited = true;
        updateHiddenKode();
    });

    document.querySelectorAll('.pic-role-select').forEach(select => {
        select.addEventListener('change', updateMandays);
    });
    $(document).on('select2:select', '.pic-role-select', updateMandays);

    


});
</script>
@endpush