<div>
    <!-- Tombol untuk buka modal -->
    <button wire:click="open" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow text-sm">
        📜 Lihat Scope of Work
    </button>

    <!-- Modal -->
    <div x-data x-show="$wire.showModal" x-transition.opacity.duration.300ms
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">

        <!-- Modal Box -->
        <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh] transform transition-all duration-300 scale-95"
            x-show="$wire.showModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">List Scope of Work</h2>
                <button type="button" @click="$wire.close()"
                    class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
            </div>

            <!-- Body -->
            <div class="flex-1 p-6 overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Scope of Work</h3>

                @if($sowProjects->isEmpty())
                    <div class="text-center py-10 text-gray-500">
                        <p>Belum ada Scope of Work.</p>
                    </div>
                @else
                    <table class="w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left">Deskripsi</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sowProjects as $sowProject)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-sm text-gray-800">
                                        {{ $sowProject->scopeOfWork->names ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800">
                                        {{ $sowProject->scopeOfWork->description ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center space-x-2">
                                        <button type="button" wire:click="edit({{ $sowProject->id }})"
                                            class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded shadow">
                                            ✏️ Edit
                                        </button>
                                        <button type="button" wire:click="delete({{ $sowProject->id }})"
                                            onclick="return confirm('Hapus SOW ini?')"
                                            class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded shadow">
                                            🗑 Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>

            <!-- Footer -->
            <div class="px-6 py-3 border-t border-gray-200 text-right">
                <button type="button" @click="$wire.close()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>