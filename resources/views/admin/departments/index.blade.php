@extends('admin.layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Department List</h1>
    <div class="flex justify-end mb-4">
        <a href="{{ route('department.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Add
            Department</a>
    </div>

    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-100">
                <th class="py-2 px-4 text-left">Department</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($departments as $data)
                <tr class="border-t">
                    <td class="py-2 px-4">{{ $data->name }}</td>
                    <td class="py-2 px-4 text-center">
                        <a href="{{ route('department.edit', $data) }}" class="text-blue-500 hover:underline">Edit</a>

                        <button onclick="confirmDelete({{ $data->id }}, '{{ $data->name }}')"
                            class="text-red-500 hover:underline ml-2">
                            Delete
                        </button>

                        <!-- Hidden delete form -->
                        <form id="delete-form-{{ $data->id }}" action="{{ route('department.destroy', $data) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete department "${name}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>

@endsection