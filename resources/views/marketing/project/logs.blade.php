@extends('marketing.layouts.app') {{-- Atau sesuaikan layoutmu --}}

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-semibold mb-6">Log Proyek: {{ $project->project_number }}</h2>

    <livewire:log-thread :project="$project" />
</div>
@endsection
