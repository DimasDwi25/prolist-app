<div x-data x-show="$wire.showModal" x-transition.opacity.duration.300ms
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    style="display: none;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl flex flex-col max-h-[90vh] transform transition-all duration-300 scale-95"
        x-show="$wire.showModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Tambah Log</h2>
            <button type="button" @click="$wire.close()" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <form wire:submit.prevent="save" class="space-y-4">

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium">Tanggal</label>
                    <input type="date" wire:model="tgl_logs"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    @error('tgl_logs') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium">Kategori</label>
                    <select wire:model="categorie_log_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('categorie_log_id') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Isi Log -->
                <div>
                    <label class="block text-sm font-medium">Isi Log</label>
                    <textarea wire:model="logs" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                    @error('logs') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Checkbox Approval -->
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="askApproval" wire:model="askApproval">
                    <label for="askApproval" class="text-sm font-medium">Minta Approval</label>
                </div>

                <!-- Response By (Hidden by default, shown by JS) -->
                <div id="responseByContainer" class="hidden">
                    <label class="block text-sm font-medium">Response By</label>
                    <select wire:model="response_by"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$wire.close()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const askApprovalCheckbox = document.getElementById('askApproval');
        const responseByContainer = document.getElementById('responseByContainer');

        function toggleResponseBy() {
            if (askApprovalCheckbox.checked) {
                responseByContainer.classList.remove('hidden');
            } else {
                responseByContainer.classList.add('hidden');
            }
        }

        askApprovalCheckbox.addEventListener('change', toggleResponseBy);
        toggleResponseBy(); // initial state
    });
</script>
@endpush