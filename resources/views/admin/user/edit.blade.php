@extends('admin.layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit User</h1>

<form action="{{ route('user.update', $user) }}" method="POST" class="bg-white p-6 rounded shadow-md w-full max-w-xl">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block mb-2 text-gray-700">Name</label>
        <input type="text" name="name" class="w-full border px-4 py-2 rounded" value="{{ old('name', $user->name) }}">
    </div>

    <div class="mb-4">
        <label class="block mb-2 text-gray-700">Email</label>
        <input type="email" name="email" class="w-full border px-4 py-2 rounded" value="{{ old('email', $user->email) }}">
    </div>

    <div class="mb-4">
        <label class="block mb-2 text-gray-700">Role</label>
        <select name="role_id" class="w-full border px-4 py-2 rounded">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected($role->id == $user->role_id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block mb-2 text-gray-700">Department</label>
        <select name="department_id" class="w-full border px-4 py-2 rounded">
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" @selected($dept->id == $user->department_id)>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('admin.user') }}" class="text-gray-600 hover:underline mr-4">Cancel</a>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
    </div>
</form>
@endsection
