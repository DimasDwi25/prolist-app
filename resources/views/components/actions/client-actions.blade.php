<div class="flex gap-2 items-center">

    <a href="#" class="text-green-600 hover:underline">👁 View</a>

    {{-- Edit Client --}}
    <a href="{{ route('client.edit', $client->id) }}" class="text-blue-600 hover:underline">✏️ Edit</a>

    {{-- Delete Quotation (only for super_admin) --}}
    @if(auth()->user()?->role?->name === 'super_admin')
        <form action="{{ route('quotation.destroy', $quotation) }}" method="POST" onsubmit="return confirm('Delete this quotation?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline">🗑 Delete</button>
        </form>
    @endif
</div>
