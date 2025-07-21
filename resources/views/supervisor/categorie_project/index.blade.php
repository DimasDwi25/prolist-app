@extends('supervisor.layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Project Categories</h1>
        <a href="{{ route('category.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            + Add Category
        </a>
        <a href="/status-projects/export"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm shadow transition">
            Export Status Projects
        </a>

    </div>

    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100">
        @livewire('supervisor-marketing.project-categorie-table')
    </div>

@endsection