<div x-data x-show="$wire.showModal" x-transition.opacity.duration.300ms
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <!-- Modal Box -->
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh] transform transition-all duration-300 scale-95"
        x-show="$wire.showModal" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Manage Sub Clients</h2>
            <button type="button" @click="$wire.closeModal()" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Form -->
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sub Client</label>
                    <input wire:model.defer="name" type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500"
                        placeholder="Masukkan nama sub client...">
                    @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex justify-end space-x-2">
                @if($editingId)
                    <button wire:click="update"
                        class="px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition">
                        Update
                    </button>
                @else
                    <button wire:click="save"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                        Simpan
                    </button>
                @endif
            </div>

            <!-- List Sub Client -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Sub Client</h3>

                @if(empty($subClients))
                    <div class="text-center py-10 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                        <p class="mt-2 text-sm">Belum ada Sub Client untuk client ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto border rounded-lg max-h-64 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($subClients as $sub)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $sub->name }}</td>
                                        <td class="px-4 py-3 text-center space-x-2">
                                            <button wire:click="edit({{ $sub->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded shadow">
                                                ✏️ Edit
                                            </button>
                                            <button wire:click="delete({{ $sub->id }})"
                                                onclick="return confirm('Hapus sub client ini?')"
                                                class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded shadow">
                                                🗑 Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 border-t border-gray-200 text-right">
            <button type="button" @click="$wire.closeModal()"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                Tutup
            </button>
        </div>
    </div>
</div>
