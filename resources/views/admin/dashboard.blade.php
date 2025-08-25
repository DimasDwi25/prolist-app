@extends('admin.layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Super Admin Dashboard</h1>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users -->
        <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h2>
            </div>
            <div class="bg-blue-500 text-white p-3 rounded-full">
                <i class="bi bi-people text-xl"></i>
            </div>
        </div>

        <!-- Roles -->
        <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm">Total Roles</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $totalRoles }}</h2>
            </div>
            <div class="bg-green-500 text-white p-3 rounded-full">
                <i class="bi bi-shield-lock text-xl"></i>
            </div>
        </div>

        <!-- Departments -->
        <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm">Departments</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $totalDepartments }}</h2>
            </div>
            <div class="bg-yellow-500 text-white p-3 rounded-full">
                <i class="bi bi-diagram-3 text-xl"></i>
            </div>
        </div>

        <!-- Clients -->
        <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-gray-500 text-sm">Clients</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $totalClients }}</h2>
            </div>
            <div class="bg-red-500 text-white p-3 rounded-full">
                <i class="bi bi-building text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Projects Overview -->
    <div class="mt-8 bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Project Overview</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-gray-500 text-sm">Total</p>
                <h3 class="text-2xl font-bold">{{ $projects['total'] }}</h3>
            </div>
            <div class="text-blue-600">
                <p class="text-gray-500 text-sm">On Progress</p>
                <h3 class="text-xl font-bold">{{ $projects['On Progress'] }}</h3>
            </div>
            <div class="text-green-600">
                <p class="text-gray-500 text-sm">Documents Completed</p>
                <h3 class="text-xl font-bold">{{ $projects['Documents Completed'] }}</h3>
            </div>
            <div class="text-indigo-600">
                <p class="text-gray-500 text-sm">Engineering Work Completed</p>
                <h3 class="text-xl font-bold">{{ $projects['Engineering Work Completed'] }}</h3>
            </div>
            <div class="text-yellow-600">
                <p class="text-gray-500 text-sm">Hold By Customer</p>
                <h3 class="text-xl font-bold">{{ $projects['Hold By Customer'] }}</h3>
            </div>
            <div class="text-red-600">
                <p class="text-gray-500 text-sm">Project Finished</p>
                <h3 class="text-xl font-bold">{{ $projects['Project Finished'] }}</h3>
            </div>
            <div class="text-orange-600">
                <p class="text-gray-500 text-sm">Material Delay</p>
                <h3 class="text-xl font-bold">{{ $projects['Material Delay'] }}</h3>
            </div>
            <div class="text-pink-600">
                <p class="text-gray-500 text-sm">Invoice On Progress</p>
                <h3 class="text-xl font-bold">{{ $projects['Invoice On Progress'] }}</h3>
            </div>
        </div>

        <!-- Chart -->
        <div class="mt-6">
            <canvas id="projectChart" class="w-full h-72"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('projectChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            'On Progress',
            'Documents Completed',
            'Engineering Work Completed',
            'Hold By Customer',
            'Project Finished',
            'Material Delay',
            'Invoice On Progress'
        ],
        datasets: [{
            data: [
                {{ $projects['On Progress'] }},
                {{ $projects['Documents Completed'] }},
                {{ $projects['Engineering Work Completed'] }},
                {{ $projects['Hold By Customer'] }},
                {{ $projects['Project Finished'] }},
                {{ $projects['Material Delay'] }},
                {{ $projects['Invoice On Progress'] }}
            ],
            backgroundColor: [
                '#3b82f6', // blue
                '#22c55e', // green
                '#6366f1', // indigo
                '#eab308', // yellow
                '#ef4444', // red
                '#f97316', // orange
                '#ec4899'  // pink
            ],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: 14 } }
            }
        }
    }
});

</script>
@endpush
