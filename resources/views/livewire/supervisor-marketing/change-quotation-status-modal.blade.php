<div
    x-data="{ open: false }"
    x-on:open-status-modal.window="
        $wire.call('openStatusModal', $event.detail.id);
    "
    x-on:open-status-modal-browser.window="open = true"
    x-on:close-status-modal.window="open = false"
>

    <div x-show="open" class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center" style="display: none;">
        <div class="bg-white rounded-lg p-6 w-96 shadow-xl z-50">
            <h2 class="text-lg font-bold mb-4">Change Quotation Status</h2>

            <div class="space-y-4">
                <select wire:model.live="status" class="w-full border border-gray-300 rounded">
                    @foreach ($statusOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-gray-500 hover:underline text-sm">
                        Cancel
                    </button>
                    <button wire:click="updateStatus" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
