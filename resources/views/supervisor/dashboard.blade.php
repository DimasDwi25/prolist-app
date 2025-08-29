@php
    $hideSensitive = Auth::user()->role->name === 'marketing_estimator';

    $roleLayouts = [
        'super_admin'          => 'admin.layouts.app',
        'marketing_director'   => 'marketing-director.layouts.app',
        'supervisor marketing' => 'supervisor.layouts.app',
        'manager_marketing'    => 'supervisor.layouts.app',
        'sales_supervisor'     => 'supervisor.layouts.app',
        'marketing_admin'      => 'supervisor.layouts.app',
        'engineering_director' => 'engineering_director.layouts.app',
        'project controller'   => 'project-controller.layouts.app',
        'engineer'             => 'engineer.layouts.app',
        'engineering_manager'  => 'project-manager.layouts.app',
        'marketing_estimator'  => 'supervisor.layouts.app',
    ];
    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';

    // Urutkan cards sesuai yang diinginkan
    $orderedCards = [
        ['title' => 'Total Quotation', 'value' => $totalQuotation, 'color' => 'blue', 'icon' => 'M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z'],
        ['title' => 'Total Project', 'value' => $totalProject, 'color' => 'orange', 'icon' => 'M9 17v-4h6v4M9 13V9h6v4M4 4h16v16H4z'],
        ['title' => 'Outstanding Quotation', 'value' => $outstandingQuotation, 'color' => 'yellow', 'icon' => 'M12 8V4m0 0l3 3m-3-3l-3 3m0 4h12M5 20h14a1 1 0 001-1v-7H4v7a1 1 0 001 1z'],
    ];

    if(!$hideSensitive) {
        $orderedCards[] = ['title' => 'Total Quotation Value', 'value' => number_format($totalQuotationValue, 0, ',', '.'), 'color' => 'green', 'icon' => 'M12 8c-4.418 0-8 1.79-8 4v4h16v-4c0-2.21-3.582-4-8-4zM12 8V4m0 0l3 3m-3-3l-3 3', 'mask' => true];
        $orderedCards[] = ['title' => 'Total Sales Value', 'value' => number_format($totalSalesValue, 0, ',', '.'), 'color' => 'purple', 'icon' => 'M3 3v18h18V3H3zm3 14h12M9 9h6v6H9V9z', 'mask' => true];
    }

    $firstRowCards = array_slice($orderedCards, 0, 3);
    $secondRowCards = array_slice($orderedCards, 3, 2);
    
@endphp

@extends($layout)

@section('content')
<div class="max-w-7xl mx-auto p-4 lg:p-6">
    <h1 class="text-xl lg:text-2xl font-bold text-gray-800 mb-6">📊 Dashboard</h1>

    {{-- Baris 1: 3 cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        @foreach($firstRowCards as $c)
        <div x-data="{ show: {{ $c['mask'] ?? false ? 'false' : 'true' }} }"
            class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 p-3 rounded-full bg-{{ $c['color'] }}-100">
                    <svg class="w-6 h-6 text-{{ $c['color'] }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 font-medium">{{ $c['title'] }}</p>
                    <h2 class="text-lg font-semibold text-gray-800 truncate">
                        @if($c['mask'] ?? false)
                            <span x-show="show" x-transition.opacity.duration.300ms>Rp {{ $c['value'] }}</span>
                            <span x-show="!show" x-transition.opacity.duration.300ms class="tracking-widest">••••</span>
                        @else
                            {{ $c['value'] }}
                        @endif
                    </h2>
                </div>
            </div>

            @if($c['mask'] ?? false)
            <button @click="show = !show" 
                    class="mt-3 text-xs font-medium text-gray-400 hover:text-{{ $c['color'] }}-600 focus:outline-none focus:ring-1 focus:ring-{{ $c['color'] }}-300 rounded transition">
                👁 Toggle
            </button>
            @endif
        </div>
        @endforeach
    </div>


    {{-- Baris 2: 2 cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach($secondRowCards as $c)
        <div x-data="{ show: {{ $c['mask'] ?? false ? 'false' : 'true' }} }"
            class="bg-white rounded-xl shadow hover:shadow-lg transition p-3 flex flex-col justify-between">
            <div class="flex items-center gap-2">
                <div class="flex-shrink-0 p-2 rounded-full bg-{{ $c['color'] }}-100">
                    <svg class="w-5 h-5 text-{{ $c['color'] }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 font-medium">{{ $c['title'] }}</p>
                    <h2 class="text-sm font-semibold text-gray-800">
                        @if($c['mask'] ?? false)
                            <span x-show="show" x-transition.opacity.duration.300ms>Rp {{ $c['value'] }}</span>
                            <span x-show="!show" x-transition.opacity.duration.300ms class="tracking-widest">••••</span>
                        @else
                            {{ $c['value'] }}
                        @endif
                    </h2>
                </div>
            </div>

            @if($c['mask'] ?? false)
            <button @click="show = !show" 
                    class="mt-2 text-xs font-medium text-gray-400 rounded transition">
                👁 Toggle
            </button>
            @endif
        </div>
        @endforeach
    </div>




    {{-- PHC Pending Validation --}}
    <div class="mt-6">
        @livewire('phc-validation-table')
        @livewire('phc-validation-modal')
    </div>

    {{-- Grafik Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        {{-- Bar Chart --}}
        <div class="bg-white shadow rounded-xl p-4">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Quotation vs Sales Value</h2>
            <canvas id="barChart" class="h-60"></canvas>
        </div>

        {{-- Pie Chart --}}
        <div class="bg-white shadow rounded-xl p-4">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Quotation Status Distribution</h2>
            <canvas id="statusPieChart" class="h-60"></canvas>
        </div>
    </div>

    {{-- Line Chart --}}
    <div class="bg-white shadow rounded-xl p-4 mt-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Monthly Trend (Quotation & Sales)</h2>
        <canvas id="lineChart" class="h-60"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const months = @json($months);
    const quotationPerMonth = @json($quotationPerMonthData);
    const salesPerMonth = @json($salesPerMonthData);

    // Bar Chart
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['Quotation', 'Sales'],
            datasets: [{
                data: [{{ $totalQuotationValue }}, {{ $totalSalesValue }}],
                backgroundColor: ['#3b82f6', '#a78bfa'],
                borderRadius: 6,
                barThickness: 40
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Pie Chart
    new Chart(document.getElementById('statusPieChart'), {
        type: 'pie',
        data: { 
            labels: {!! json_encode($labels) !!},
            datasets: [{ data: {!! json_encode($data) !!}, backgroundColor: {!! json_encode($colors) !!} }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                { label: 'Quotation', data: quotationPerMonth, borderColor: '#3b82f6', fill: true, backgroundColor: 'rgba(59,130,246,0.1)' },
                { label: 'Sales', data: salesPerMonth, borderColor: '#10b981', fill: true, backgroundColor: 'rgba(16,185,129,0.1)' }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
@endsection
