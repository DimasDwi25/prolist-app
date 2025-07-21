<div x-data x-show="$wire.showModal" x-transition.opacity.duration.300ms
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" {{--
    supaya tidak kelihatan sebelum x-show aktif --}} >
    <!-- Modal Box -->
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh] transform transition-all duration-300 scale-95"
        x-show="$wire.showModal" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Manage Scope of Work</h2>
            <button type="button" @click="$wire.close()" class="text-gray-500 hover:text-gray-700 text-xl">
                ✕
            </button>
        </div>

        <!-- Modal Content (Scrollable Area) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Form Input -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Deskripsi -->
                <div class="col-span-1 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <input type="text" wire:model="description"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Masukkan deskripsi">
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" wire:model="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Masukkan kategori">
                    @error('category')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Items -->
                <div class="col-span-1 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Items</label>
                    <textarea wire:model="items" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm resize-none focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Daftar item pekerjaan, pisahkan per baris"></textarea>
                    @error('items')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex justify-end">
                <button type="button" wire:click="save"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors duration-200">
                    Simpan Scope of Work
                </button>
            </div>

            <!-- List SOW -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Scope of Work</h3>

                @if($sows->isEmpty())
                    <div class="text-center py-10 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m5 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v12a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm">Belum ada Scope of Work. Tambahkan di atas.</p>
                    </div>
                @else
                    <div class="overflow-x-auto border rounded-lg max-h-64 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Deskripsi
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($sows as $sow)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $sow->description }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $sow->category }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-pre-line">{{ $sow->items }}</td>
                                        <td class="px-4 py-3 text-center space-x-2">
                                            <button type="button" wire:click="edit({{ $sow->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded shadow">
                                                ✏️ Edit
                                            </button>
                                            <button type="button" wire:click="delete({{ $sow->id }})"
                                                onclick="return confirm('Hapus SOW ini?')"
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
            <button type="button" @click="$wire.close()"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                Tutup
            </button>
        </div>
    </div>
</div>
