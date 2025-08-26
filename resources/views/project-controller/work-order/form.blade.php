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

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Work Order' : 'Create New Work Order' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $isEdit ? 'Update the work order details below' : 'Fill in the form to create a new work order' }}
                </p>
            </div>
            <a href="{{ route('engineer.work_order') }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Work Orders
            </a>
        </div>

        <form
            action="{{ $isEdit ? route('engineer.work-orders.update', $workOrder) : route('engineer.work-orders.store') }}"
            method="POST"
            class="space-y-6">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <!-- Project & Client Section -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" id="project_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->pn_number }}" @selected(old('project_id', $workOrder->project_id ?? '') == $project->pn_number)>
                                {{ $project->project_number }} - {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                    <input type="text" name="client_name" id="client_name" readonly
                        class="w-full rounded-md bg-gray-50 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <span id="wo_kode_prefix" class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-mono">
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

            <!-- PIC and Roles Section -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Person In Charge (PIC)</h3>
                
                <div class="space-y-4">
                    @foreach(range(1, 5) as $i)
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIC {{ $i }}</label>
                            <select name="pic{{ $i }}" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Team Member</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('pic' . $i, $workOrder->{'pic' . $i} ?? '') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role for PIC {{ $i }}</label>
                            <select name="role_pic_{{ $i }}" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pic-role-select">
                                <option value="">Select Role</option>
                                @foreach ($roles->where('type_role', 2) as $role)
                                    <option value="{{ $role->id }}" data-role-name="{{ strtolower($role->name) }}"
                                        @selected(old('role_pic_' . $i, $workOrder->{'role_pic_' . $i} ?? '') == $role->id)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Mandays Calculation -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Engineer Mandays</label>
                    <input type="text" name="total_mandays_eng" readonly
                        class="w-full rounded-md bg-gray-50 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Electrician Mandays</label>
                    <input type="text" name="total_mandays_elect" readonly
                        class="w-full rounded-md bg-gray-50 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Work Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Work Description</label>
                <textarea name="work_description" rows="4"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('work_description', $workOrder->work_description ?? '') }}</textarea>
            </div>

            <!-- Submit Button with Log Modal -->
            <div class="pt-4">
                <button type="button" id="submit-button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $isEdit ? 'Update Work Order' : 'Create Work Order' }}
                </button>
            </div>

            <!-- Log Modal (hidden by default) -->
            <div id="log-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Save to Project Log</h3>
                            <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <p class="text-sm text-gray-500">Would you like to save the work description to the project log?</p>
                            
                            <div id="log-fields" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Log Category</label>
                                    <select name="categorie_log_id" id="categorie_log_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Category</option>
                                        @foreach($categorieLogs as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="open">Open</option>
                                        <option value="close">Close</option>
                                    </select>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="need_response" id="need_response" value="1"
                                        class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <label for="need_response" class="ml-2 block text-sm text-gray-700">Requires Response</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" name="save_log" value="yes"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save to Log
                        </button>
                        <button type="submit" name="save_log" value="no"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Skip Log
                        </button>
                        <button type="button" id="cancel-modal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
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

    // Inisialisasi semua select2
    $('.select2').select2({
        theme: "classic", // bisa "bootstrap-5" kalau pakai bootstrap theme
        width: '100%',
        placeholder: 'Pilih opsi',
        allowClear: true
    });


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