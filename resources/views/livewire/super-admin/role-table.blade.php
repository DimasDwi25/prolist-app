<div class="flex space-x-2 justify-center">
    {{-- Tombol Edit --}}
    <a href="{{ route('role.edit', $role->id) }}"
        class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded">
        Edit
    </a>

    {{-- Tombol Delete --}}
    <form action="{{ route('role.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded">
            Delete
        </button>
    </form>
</div>
