<div class="space-x-2">
    <a href="{{ route('supervisor.project.show', $project) }}"
       class="inline-flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium">
        👁️ View
    </a>
    <a href="{{ route('project.edit', $project) }}"
       class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
        ✏️ Edit
    </a>
</div>
