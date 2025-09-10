<button 
    x-data 
    x-on:click="$wire.call('openClientModal', {{ $client->id }})"
    class="px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600 transition"
>
    Edit
</button>
