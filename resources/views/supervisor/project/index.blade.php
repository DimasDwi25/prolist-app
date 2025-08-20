@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Project List</h1>
        <a href="{{ route('project.create') }}"
            class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
            + Add Project
        </a>
    </div>

    <div class="flex flex-col md:flex-row justify-between gap-2 mb-4">
        <a href="/projects/export" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm shadow">
            ⬇️ Export
        </a>

        <form action="/projects/import" method="POST" enctype="multipart/form-data" class="flex gap-2">
            @csrf
            <input type="file" name="file" required class="border rounded px-2 py-1 text-sm">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">📥 Import</button>
        </form>
    </div>


    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 font-medium shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100" >
        @livewire('supervisor-marketing.project-table')
    </div>

@endsection