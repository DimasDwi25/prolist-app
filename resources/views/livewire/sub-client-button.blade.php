<button
    wire:click="$dispatch('open-sub-client-modal', { clientId: {{ $clientId }} })"
    type="button"
    class="text-white bg-blue-500 px-2 py-1 rounded"
>
    ➕ Sub Client
</button>
