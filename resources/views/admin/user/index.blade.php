@extends('admin.layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">User List</h1>

<div class="flex justify-end mb-4">
    <a href="{{ route('user.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Add User</a>
</div>

<table class="min-w-full bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-100">
            <th class="py-2 px-4 text-left">Name</th>
            <th class="py-2 px-4 text-left">Email</th>
            <th class="py-2 px-4 text-left">Role</th>
            <th class="py-2 px-4 text-left">Department</th>
            <th class="py-2 px-4">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr class="border-t">
            <td class="py-2 px-4">{{ $user->name }}</td>
            <td class="py-2 px-4">{{ $user->email }}</td>
            <td class="py-2 px-4">{{ $user->role->name ?? '-' }}</td>
            <td class="py-2 px-4">{{ $user->department->name ?? '-' }}</td>
            <td class="py-2 px-4 text-center">
                <a href="{{ route('user.edit', $user) }}" class="text-blue-500 hover:underline">Edit</a>
                <form action="{{ route('user.destroy', $user) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this user?')" class="text-red-500 hover:underline ml-2">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
