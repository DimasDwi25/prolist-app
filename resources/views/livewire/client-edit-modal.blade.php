<div
    x-data="{ open: false }"
    x-on:open-client-modal-browser.window="open = true"
    x-on:close-client-modal.window="open = false"
>
    <div x-show="open" class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center" style="display: none;">
        <div class="bg-white rounded-lg p-6 w-96 shadow-xl z-50">
            <h2 class="text-lg font-bold mb-4">Edit Client</h2>

            <div class="space-y-3">
                <input type="text" wire:model="name" placeholder="Name" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="phone" placeholder="Phone" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="address" placeholder="Address" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="client_representative" placeholder="Client Rep" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="city" placeholder="City" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="province" placeholder="Province" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="country" placeholder="Country" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="zip_code" placeholder="Zip Code" class="w-full border px-2 py-1 rounded">
                <input type="text" wire:model="web" placeholder="Website" class="w-full border px-2 py-1 rounded">
                <textarea wire:model="notes" placeholder="Notes" class="w-full border px-2 py-1 rounded"></textarea>

                <div class="flex justify-end gap-3 mt-3">
                    <button type="button" @click="open = false" class="text-gray-500 hover:underline text-sm">Cancel</button>
                    <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
