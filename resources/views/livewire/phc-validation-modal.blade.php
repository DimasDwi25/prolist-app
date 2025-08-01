<div x-data="{ open: @entangle('showModal') }" x-show="open"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6" @click.away="open = false">
        <h2 class="text-lg font-bold mb-4">Masukkan PIN</h2>
        <input type="password" wire:model="pin" class="w-full border rounded p-2 mb-2" placeholder="PIN Anda">
        @error('pin') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <div class="flex justify-end gap-2 mt-4">
            <button @click="open = false" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
            <button wire:click="validatePin" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                Konfirmasi
            </button>
        </div>
    </div>
</div>