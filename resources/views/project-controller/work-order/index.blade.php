@php
    $roleLayouts = [
        'project controller'     => 'project-controller.layouts.app',
        'engineer'     => 'engineer.layouts.app',
        'engineering_manager'         => 'project-manager.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">📄 Work Orders</h1>
        <a href="{{ route('engineer.work-orders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">➕ Create WO</a>
    </div>

    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100">
        @livewire('project-controller.work-order-table')
    </div>
</div>
@endsection
