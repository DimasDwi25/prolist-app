@extends('admin.layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Client List</h1>
        <a href="{{ route('role.create') }}"
            class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
            + Add Role
        </a>
    </div>

    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100">
        @livewire('super-admin.role-table')
    </div>
@endsection
