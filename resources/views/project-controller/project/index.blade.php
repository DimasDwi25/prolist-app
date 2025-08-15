@extends('project-controller.layouts.app')

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