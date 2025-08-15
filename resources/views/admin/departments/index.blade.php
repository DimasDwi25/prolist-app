@extends('admin.layouts.app')

@section('content')

{{-- Import Form --}}
    <form action="{{ route('department.import') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-4 border border-gray-200 rounded-xl shadow-sm mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
        @csrf
        <label class="text-sm text-gray-700 font-medium">📥 Import Excel:</label>
        <input type="file" name="file" required
            class="block text-sm file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-md file:font-semibold file:border-0 file:cursor-pointer border border-gray-300 rounded-md w-full sm:w-auto">
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow transition w-full sm:w-auto">
            Import
        </button>
    </form>

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