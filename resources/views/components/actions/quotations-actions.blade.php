<div class="flex justify-center space-x-2">
    <a href="{{ route('quotation.edit', $quotation) }}"
       class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded shadow transition">
        ✏️ Edit
    </a>

    @if(Auth::user()->role->name === 'super_admin')
        <form action="{{ route('quotation.destroy', $quotation) }}" method="POST" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded shadow transition">
                🗑 Delete
            </button>
        </form>
    @endif
</div>
