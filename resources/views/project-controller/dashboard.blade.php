@extends('project-controller.layouts.app')

@section('content')
    <div class="space-y-6">
        @livewire('phc-validation-table')
    </div>

    @livewire('phc-validation-modal')
@endsection