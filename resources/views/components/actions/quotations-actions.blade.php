@php
    $allowedRoles = ['super_admin', 'marketing_director'];
@endphp
<div class="flex justify-center space-x-2">
    @if(in_array(Auth::user()->role->name, $allowedRoles))
        <a href="{{ route('quotation.edit', $quotation) }}"
            class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded shadow transition">
            ✏️ Edit
        </a>

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