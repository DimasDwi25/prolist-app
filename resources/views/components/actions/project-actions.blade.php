@php
    $allowedRoles = ['super_admin', 'marketing_director'];
@endphp
<div class="space-x-2">
    <a href="{{ route('supervisor.project.show', $project) }}"
       class="inline-flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium">
        👁️ View
    </a>
    @if(in_array(Auth::user()->role->name, $allowedRoles))
    <a href="{{ route('project.edit', $project) }}"
       class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
        ✏️ Edit
    </a>
    @endif

    {{-- @if(Auth::user()->role->name === 'super_admin')
        <form action="{{ route('project.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded shadow transition">
                🗑 Delete
            </button>
        </form>
    @endif --}}
</div>
