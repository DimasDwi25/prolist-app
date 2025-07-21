@extends('project-controller.layouts.app')

@section('content')
    @php $isEdit = isset($workOrder); @endphp

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $isEdit ? '✏️ Edit Work Order' : '➕ Create Work Order' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Lengkapi data berikut untuk {{ $isEdit ? 'memperbarui' : 'membuat' }}
                    WO</p>
            </div>
            <a href="{{ route('project_controller.work_order') }}"
                class="text-sm text-gray-600 hover:text-blue-600 hover:underline">←
                Kembali ke daftar WO</a>
        </div>

        <form
            action="{{ $isEdit ? route('project_controller.work-orders.update', $workOrder) : route('project_controller.work-orders.store') }}"
            method="POST">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- Project & Client --}}
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <x-form.select name="project_id" label="Project" :options="$projects" option-value="id"
                    option-label="project_number" :selected="old('project_id', $workOrder->project_id ?? '')" required />

                <x-input.text name="client_name" id="client_name" label="Client" readonly />

            </div>

            {{-- WO Date & Kode --}}
            <div class="grid md:grid-cols-3 gap-6 mb-6">
                <x-input.date name="wo_date" label="WO Date" :value="old('wo_date', $workOrder->wo_date ?? '')" />

                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">WO Kode</label>
                    <div class="flex items-center">
                        <span id="wo_kode_prefix"
                            class="inline-flex items-center bg-gray-100 px-3 py-2 rounded-l border border-r-0 border-gray-300 text-gray-700 text-sm font-mono">
                            WO-00/000/
                        </span>
                        <input type="number" id="wo_number_last" name="wo_number_last"
                            class="w-24 border-gray-300 rounded-r-md shadow-sm"
                            value="{{ old('wo_number_last') ?? (isset($workOrder) ? explode('/', $workOrder->wo_kode_no)[2] : '') }}"
                            min="1">
                    </div>
                </div>
            </div>

            {{-- Hidden input untuk dikirim --}}
            <input type="hidden" name="wo_kode_no" id="wo_kode_no_real"
                value="{{ old('wo_kode_no', $workOrder->wo_kode_no ?? '') }}">

            {{-- PIC dan Role --}}
            <h3 class="text-lg font-semibold text-gray-700 mb-2">👥 PIC dan Role</h3>
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                @foreach(range(1, 5) as $i)
                    <x-form.select name="pic{{ $i }}" label="PIC {{ $i }}" :options="$users" :selected="old('pic' . $i, $workOrder->{'pic' . $i} ?? '')" />

                    {{-- Ganti role select ke ini --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role PIC {{ $i }}</label>
                        <select name="role_pic_{{ $i }}" class="w-full border-gray-300 rounded-md shadow-sm"
                            onchange="updateMandays()">
                            <option value="">- Pilih Role -</option>
                            @foreach ($roles->where('type_role', 2) as $role)
                                <option value="{{ $role->id }}" data-role-name="{{ strtolower($role->name) }}"
                                    @selected(old('role_pic_' . $i, $workOrder->{'role_pic_' . $i} ?? '') == $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

            </div>

            {{-- Mandays --}}
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <x-input.text name="total_mandays_eng" label="🛠️ Total Mandays Engineer" readonly />
                <x-input.text name="total_mandays_elect" label="⚡ Total Mandays Elect" readonly />

            </div>

            {{-- Work Description --}}
            <div class="mb-6">
                <label class="text-sm font-medium text-gray-700 block mb-1">📝 Work Description</label>
                <textarea name="work_description" rows="4"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2">{{ old('work_description', $workOrder->work_description ?? '') }}</textarea>
            </div>

            <div x-data="{ open: false }">
                <button type="button" @click="open = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow">
                    💾 Submit
                </button>

                <!-- Modal -->
                <div x-show="open" x-cloak
                    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Konfirmasi Simpan ke Log</h3>
                        <p class="text-gray-600 mb-4">Apakah Anda ingin menyimpan *Work Description* ke log project?</p>

                        <!-- Field tambahan -->
                        <div id="log-fields">
                            <!-- Work Description (readonly) -->
                            
                            <!-- Kategori Log -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Log</label>
                                <select name="categorie_log_id" id="categorie_log_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categorieLogs as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="open">Open</option>
                                    <option value="close">Close</option>
                                </select>
                            </div>

                            <!-- Need Response -->
                            <div class="mb-4 flex items-center gap-2">
                                <input type="checkbox" name="need_response" id="need_response" value="1"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="need_response" class="text-sm text-gray-700">Perlu Respon?</label>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex justify-end gap-3">
                            <button type="submit" name="save_log" value="yes"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                                onclick="return validateLogFields()">Ya, Simpan ke Log</button>
                            <button type="submit" name="save_log" value="no"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Tidak</button>
                            <button type="button" @click="open = false"
                                class="border border-gray-300 px-4 py-2 rounded">Batal</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function validateLogFields() {
                    const cat = document.getElementById('categorie_log_id');
                    if (!cat.value) {
                        alert('Pilih kategori log terlebih dahulu!');
                        return false;
                    }
                    return true;
                }
            </script>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Stop Livewire sebelum Turbo cache halaman (hindari double instance)
        document.addEventListener('turbo:before-cache', () => {
            window.Livewire?.stop();
        });

        document.addEventListener('turbo:load', () => {
            const projectSelect = document.querySelector('select[name="project_id"]');
            const clientInput = document.getElementById('client_name');
            const woKodePrefix = document.getElementById('wo_kode_prefix');
            const woNumberLast = document.getElementById('wo_number_last');
            const woKodeReal = document.getElementById('wo_kode_no_real');
            const projects = @json($projects);
            const projectWorkOrderCounts = @json($projectWorkOrderCounts);
            const year = new Date().getFullYear().toString().slice(-2);

            if (!projectSelect) return;

            function updatePrefix() {
                const pid = projectSelect.value;
                const project = projects.find(p => p.id == pid);
                if (!project) return;

                const match = project.project_number.match(/^PN-\d{2}\/(\d{3})$/);
                const code = match ? match[1] : '000';
                const prefix = `WO-${year}/${code}/`;
                woKodePrefix.innerText = prefix;

                const defaultLast = parseInt(projectWorkOrderCounts[pid] ?? 0) + 1;
                if (!woNumberLast.dataset.edited || woNumberLast.value === '') {
                    woNumberLast.value = defaultLast;
                }
                updateHiddenKode();
            }

            function updateHiddenKode() {
                woKodeReal.value = woKodePrefix.innerText + (woNumberLast.value || '1');
            }

            function fetchClient() {
                if (!projectSelect.value) return;
                fetch(`/projects/${projectSelect.value}/client`)
                    .then(res => res.json())
                    .then(data => clientInput.value = data.client_name ?? '');
            }

            // Unbind dulu agar nggak double saat Turbo load
            projectSelect.removeEventListener('change', handleChange);
            woNumberLast.removeEventListener('input', updateHiddenKode);

            function handleChange() {
                updatePrefix();
                fetchClient();
            }

            // Bind ulang event listener
            projectSelect.addEventListener('change', handleChange);
            woNumberLast.addEventListener('input', updateHiddenKode);

            // Init saat halaman pertama dimuat
            updatePrefix();
            fetchClient();

            // Hitung Mandays
            updateMandays();
        });

        function updateMandays() {
            let totalEng = 0, totalElect = 0;
            for (let i = 1; i <= 5; i++) {
                const select = document.querySelector(`select[name="role_pic_${i}"]`);
                const selected = select?.options[select.selectedIndex];
                const roleName = selected?.dataset.roleName?.toLowerCase();
                if (roleName === 'engineer') totalEng++;
                else if (roleName === 'electrician') totalElect++;
            }
            document.querySelector('input[name="total_mandays_eng"]').value = totalEng;
            document.querySelector('input[name="total_mandays_elect"]').value = totalElect;
        }
    </script>
@endpush