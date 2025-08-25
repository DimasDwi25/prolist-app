@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
        'marketing_admin'         => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
  <div class="max-w-7xl mx-auto p-6 lg:p-10">
    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-8">📊 Dashboard</h1>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- Total Quotation --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-blue-100 rounded-full">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z" />
      </svg>
      </div>
      <div>
      <p class="text-gray-500 text-sm">Total Quotation</p>
      <h2 class="text-2xl font-bold text-gray-800">{{ $totalQuotation }}</h2>
      </div>
    </div>

    {{-- Total Quotation Value (Modern Hidden - Updated Layout) --}}
    <div x-data="{ show: false }" class="bg-white shadow rounded-2xl p-6 hover:shadow-lg transition">
      <div class="flex items-start gap-4">
      <div class="p-4 bg-green-100 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M12 8c-4.418 0-8 1.79-8 4v4h16v-4c0-2.21-3.582-4-8-4zM12 8V4m0 0l3 3m-3-3l-3 3" />
        </svg>
      </div>
      <div class="flex-1">
        <p class="text-gray-500 text-sm">Total Quotation Value</p>
        <h2 class="text-2xl font-bold text-gray-800 flex flex-nowrap items-center space-x-1">
        <span>Rp</span>
        <template x-if="show">
          <span class="truncate">{{ number_format($totalQuotationValue, 0, ',', '.') }}</span>
        </template>
        <template x-if="!show">
          <span class="tracking-widest">••••</span>
        </template>
        </h2>
        <div class="mt-2">
        <button @click="show = !show" class="text-gray-600 hover:text-blue-600 transition focus:outline-none">
          <template x-if="show">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.313.25-2.563.7-3.7m3.09 6.46A3 3 0 0012 15c.795 0 1.53-.312 2.09-.873M21.3 17.3A9.953 9.953 0 0022 9c0-5.523-4.477-10-10-10a9.954 9.954 0 00-7.1 2.9M1 1l22 22" />
          </svg>
          </template>
          <template x-if="!show">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
          </svg>
          </template>
        </button>
        </div>
      </div>
      </div>
    </div>



    {{-- Total Sales Value (Modern Hidden - Updated Layout) --}}
    <div x-data="{ show: false }" class="bg-white shadow rounded-2xl p-6 hover:shadow-lg transition">
      <div class="flex items-start gap-4">
      <div class="p-4 bg-purple-100 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M3 3v18h18V3H3zm3 14h12M9 9h6v6H9V9z" />
        </svg>
      </div>
      <div class="flex-1">
        <p class="text-gray-500 text-sm">Total Sales Value</p>
        <h2 class="text-2xl font-bold text-gray-800 flex flex-nowrap items-center space-x-1">
        <span>Rp</span>
        <template x-if="show">
          <span class="truncate">{{ number_format($totalSalesValue, 0, ',', '.') }}</span>
        </template>
        <template x-if="!show">
          <span class="tracking-widest">••••</span>
        </template>
        </h2>
        <div class="mt-2">
        <button @click="show = !show" class="text-gray-600 hover:text-blue-600 transition focus:outline-none">
          <template x-if="show">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.313.25-2.563.7-3.7m3.09 6.46A3 3 0 0012 15c.795 0 1.53-.312 2.09-.873M21.3 17.3A9.953 9.953 0 0022 9c0-5.523-4.477-10-10-10a9.954 9.954 0 00-7.1 2.9M1 1l22 22" />
          </svg>
          </template>
          <template x-if="!show">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
          </svg>
          </template>
        </button>
        </div>
      </div>
      </div>
    </div>

    {{-- Total Project --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-orange-100 rounded-full">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M9 17v-4h6v4M9 13V9h6v4M4 4h16v16H4z" />
      </svg>
      </div>
      <div>
      <p class="text-gray-500 text-sm">Total Project</p>
      <h2 class="text-2xl font-bold text-gray-800">{{ $totalProject }}</h2>
      </div>
    </div>

    {{-- Outstanding Quotation --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-yellow-100 rounded-full">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 8V4m0 0l3 3m-3-3l-3 3m0 4h12M5 20h14a1 1 0 001-1v-7H4v7a1 1 0 001 1z" />
      </svg>
      </div>
      <div>
      <p class="text-gray-500 text-sm">Outstanding Quotation</p>
      <h2 class="text-2xl font-bold text-gray-800">{{ $outstandingQuotation }}</h2>
      </div>
    </div>
    </div>

    {{-- PHC Pending Validation Section --}}
    <div class="mt-12">
    @livewire('phc-validation-table')
    @livewire('phc-validation-modal')
    </div>

    {{-- Grafik Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
    {{-- Bar Chart --}}
    <div class="bg-white shadow rounded-2xl p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Quotation vs Sales Value</h2>
      <div class="relative h-72 w-full">
      <canvas id="barChart"></canvas>
      </div>
    </div>

    {{-- Pie Chart --}}
    <div class="bg-white shadow rounded-2xl p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Quotation Status Distribution</h2>
      <canvas id="statusPieChart"></canvas>
    </div>
    </div>

    {{-- Line Chart (Trend Bulanan) --}}
    <div class="bg-white shadow rounded-2xl p-6 mt-10">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Monthly Trend (Quotation & Sales)</h2>
    <canvas id="lineChart"></canvas>
    </div>


  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>

    document.addEventListener('DOMContentLoaded', function () {
    const months = @json($months);
    const quotationPerMonth = @json($quotationPerMonthData);
    const salesPerMonth = @json($salesPerMonthData);

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');

    new Chart(barCtx, {
      type: 'bar',
      data: {
      labels: ['Quotation Value', 'Sales Value'],
      datasets: [{
        label: 'Amount (Rp)',
        data: [{{ $totalQuotationValue }}, {{ $totalSalesValue }}],
        backgroundColor: ['#3b82f6', '#a78bfa'], // Soft blue & purple
        borderRadius: 8,
        barThickness: 50
      }]
      },
      options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
        display: false
        },
        tooltip: {
        callbacks: {
          label: function (context) {
          const val = context.raw;
          return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
          }
        }
        }
      },
      scales: {
        y: {
        beginAtZero: true,
        ticks: {
          callback: function (value) {
          // Format with suffix (K, M, B)
          if (value >= 1_000_000_000) return (value / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + ' Miliar';
          if (value >= 1_000_000) return (value / 1_000_000).toFixed(1).replace(/\.0$/, '') + ' Juta';
          if (value >= 1_000) return (value / 1_000).toFixed(1).replace(/\.0$/, '') + ' Ribu';
          return value.toString();
          },
          color: '#6b7280',
        },
        grid: {
          color: '#e5e7eb'
        }
        },
        x: {
        ticks: {
          color: '#6b7280',
        },
        grid: {
          display: false
        }
        }
      }
      }
    });

    // Pie Chart
    const ctx = document.getElementById('statusPieChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
      labels: {!! json_encode($labels) !!},
      datasets: [{
        data: {!! json_encode($data) !!},
        backgroundColor: {!! json_encode($colors) !!},
        hoverOffset: 6
      }]
      },
      options: {
      responsive: true,
      plugins: {
        legend: {
        position: 'bottom'
        }
      }
      }
    });

    // Line Chart
    new Chart(document.getElementById('lineChart').getContext('2d'), {
      type: 'line',
      data: {
      labels: months,
      datasets: [
        {
        label: 'Quotation',
        data: quotationPerMonth,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
        fill: true,
        tension: 0.3
        },
        {
        label: 'Sales',
        data: salesPerMonth,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        fill: true,
        tension: 0.3
        }
      ]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
    });
    });
  </script>
@endsection