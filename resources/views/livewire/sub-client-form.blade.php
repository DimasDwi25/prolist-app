<div>
    <dialog id="subClientModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">
                {{ $subClientId ? 'Edit Sub Client' : 'Tambah Sub Client' }}
            </h3>

            <div class="form-control mb-2">
                <label class="label">Nama</label>
                <input type="text" wire:model="name" class="input input-bordered" />
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-control mb-4">
                <label class="label">Email</label>
                <input type="email" wire:model="email" class="input input-bordered" />
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="modal-action">
                <button class="btn" wire:click="save">Simpan</button>
                <form method="dialog">
                    <button class="btn">Tutup</button>
                </form>
            </div>
        </div>
    </dialog>
</div>

@push('scripts')
<script>
    Livewire.on('showSubClientModal', () => {
        document.getElementById('subClientModal').showModal();
    });
    Livewire.on('hideSubClientModal', () => {
        document.getElementById('subClientModal').close();
    });
</script>
@endpush
