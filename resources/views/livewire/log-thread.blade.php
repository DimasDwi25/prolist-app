<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-10">
    <div class="mb-4">
        @php
            $role = Auth::user()->role->name ?? '';
        @endphp

        <a href="{{ $role === 'engineer'
    ? route('engineer.project.show', $project->id)
    : route('project.show', $project->id) }}"
            class="inline-flex items-center bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 text-sm">
            ← Kembali
        </a>
    </div>


    {{-- 📝 Form Tambah Log --}}
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            📝 <span>Tambah Log</span>
        </h2>

        <div class="grid gap-6">
            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Log</label>
                <select wire:model="categoryId"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(\App\Models\CategorieLog::all() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Perlu Respons --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" wire:model="need_response" id="need_response"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <label for="need_response" class="text-sm font-medium text-gray-700">Perlu Respons?</label>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Direspon oleh</label>
                <select wire:model="responseBy"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih User --</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('responseBy') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Isi Log --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Log</label>
                <textarea wire:model="logContent" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="Tulis log kamu di sini..."></textarea>
                @error('logContent')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="text-right">
                <button wire:click="{{ $editingLogId ? 'updateLog' : 'saveLog' }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-sm transition">
                    {{ $editingLogId ? '✏️ Update Log' : '💾 Simpan Log' }}
                </button>
            </div>
        </div>
    </div>

    {{-- 📜 Daftar Log --}}
    <div class="bg-blue-50 rounded-xl p-6 shadow-inner border border-blue-100 space-y-12">
        @forelse($logs->groupBy(fn($log) => \Carbon\Carbon::parse($log->tgl_logs)->format('Y-m-d')) as $date => $groupedLogs)
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-blue-600 rounded-full"></div>
                    <h3 class="text-xl font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </h3>
                </div>

                <div class="space-y-4 border-l-4 border-blue-200 pl-6">
                    @foreach($groupedLogs as $log)
                        <div
                            class="bg-white hover:bg-blue-100 border border-blue-100 rounded-lg p-5 shadow-sm hover:shadow-md transition-all duration-200">

                            {{-- Header Log --}}
                            <div class="flex justify-between items-center text-sm mb-2">
                                <div class="font-medium text-gray-700">
                                    🕒 {{ \Carbon\Carbon::parse($log->tgl_logs)->format('d M Y H:i') }}
                                </div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $log->status === 'open' ? 'yellow' : 'green' }}-100 text-{{ $log->status === 'open' ? 'yellow' : 'green' }}-800">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </div>

                            {{-- Isi Log --}}
                            <div class="text-gray-800 text-sm whitespace-pre-line mb-2">
                                <strong>📝 Log:</strong><br>
                                {{ $log->logs }}
                            </div>

                            {{-- Detail Lainnya --}}
                            <div class="text-sm text-gray-600 space-y-1">
                                <div>🏷️ <strong>Kategori:</strong> {{ $log->category->name }}</div>
                                <div>👤 <strong>Dibuat oleh:</strong> {{ $log->user->name }}</div>

                                @if ($log->need_response && $log->responseUser)
                                    <div>💬 <strong>Respons oleh:</strong> {{ $log->responseUser->name }}</div>
                                @endif

                                @if ($log->status === 'close' && $log->closingUser)
                                    <div>🔒 <strong>Ditutup oleh:</strong> {{ $log->closingUser->name }}</div>
                                    <div>📅 <strong>Tanggal Tutup:</strong>
                                        {{ \Carbon\Carbon::parse($log->closing_date)->translatedFormat('l, d F Y H:i') }}</div>
                                @endif
                            </div>


                            {{-- Tombol Aksi --}}
                            @if(Auth::id() === $log->users_id && $log->status === 'open')
                                <div class="flex justify-end gap-2 mt-3 text-sm">
                                    <button wire:click="editLog({{ $log->id }})"
                                        class="px-3 py-1 rounded bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-medium transition">
                                        ✏️ Edit
                                    </button>
                                    <button wire:click="closeLog({{ $log->id }})"
                                        class="px-3 py-1 rounded bg-red-100 hover:bg-red-200 text-red-800 font-medium transition">
                                        🔒 Close
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 text-sm">
                Tidak ada log yang ditambahkan.
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('log-error', data => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
            });
        });

        Livewire.on('log-success', data => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            Echo.channel('logs.project.{{ $project->id }}')
                .listen('.log.created', (e) => {
                    Livewire.dispatch('refreshLogs');
                })
                .listen('.log.updated', (e) => {
                    Livewire.dispatch('refreshLogs');
                })
                .listen('.log.closed', (e) => {
                    Livewire.dispatch('refreshLogs');
                });

        });

    </script>

@endpush