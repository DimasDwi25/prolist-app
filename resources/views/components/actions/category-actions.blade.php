<div class="flex justify-end items-center gap-2">
    <a href="{{ route('category.edit', $category->id) }}"
        class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition">
        ✏️ Edit
    </a>

    @can('delete', $category)
        <form method="POST" action="{{ route('category.destroy', $category->id) }}"
              onsubmit="return confirm('Are you sure you want to delete this category?')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition">
                🗑️ Delete
            </button>
        </form>
    @endcan
</div>
