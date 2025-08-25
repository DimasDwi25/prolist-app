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


    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 font-medium shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100">
        @livewire('project-controller.project-table')
    </div>

@endsection