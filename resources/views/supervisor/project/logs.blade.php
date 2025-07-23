@extends(match(Auth::user()->role->name) {
    'super_admin' => 'admin.layouts.app',
    'supervisor marketing' => 'supervisor.layouts.app',
    'engineer' => 'engineer.layouts.app',
    'project controller' => 'project-controller.layouts.app',
    default => 'layouts.app', // fallback jika role tidak cocok
})

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-semibold mb-6">Log Proyek: {{ $project->project_number }}</h2>

    <livewire:log-thread :project="$project" />
</div>
@endsection
