<div class="flex gap-2">
    <a href="{{ route('quotation.show', $quotation) }}" class="text-green-600 hover:underline">👁 View</a>
    <a href="{{ route('quotation.edit', $quotation) }}" class="text-blue-600 hover:underline">✏️ Edit</a>

    @if(auth()->user()?->role?->name === 'super_admin')
        <form action="{{ route('quotation.destroy', $quotation) }}" method="POST" onsubmit="return confirm('Delete this quotation?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline">🗑 Delete</button>
        </form>
    @endif
</div>
