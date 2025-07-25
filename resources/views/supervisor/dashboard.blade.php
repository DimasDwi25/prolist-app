@extends('supervisor.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 lg:p-10">
  <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-8">📊 Dashboard</h1>

  {{-- Statistik Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- Total Quotation --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-blue-100 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z" />
        </svg>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Total Quotation</p>
        <h2 class="text-2xl font-bold text-gray-800">{{ $totalQuotation }}</h2>
      </div>
    </div>

    {{-- Total Quotation Value --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-green-100 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8c-4.418 0-8 1.79-8 4v4h16v-4c0-2.21-3.582-4-8-4zM12 8V4m0 0l3 3m-3-3l-3 3" />
        </svg>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Total Quotation Value</p>
        <h2 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalQuotationValue, 0, ',', '.') }}</h2>
      </div>
    </div>

    {{-- Total Sales Value --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-purple-100 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3v18h18V3H3zm3 14h12M9 9h6v6H9V9z" />
        </svg>
      </div>
      <div>
        <p class="text-gray-500 text-sm">Total Sales Value</p>
        <h2 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalSalesValue, 0, ',', '.') }}</h2>
      </div>
    </div>

    {{-- Total Project --}}
    <div class="bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition">
      <div class="p-4 bg-orange-100 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

  {{-- Grafik Section --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
    {{-- Bar Chart --}}
    <div class="bg-white shadow rounded-2xl p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Quotation vs Sales Value</h2>
      <canvas id="barChart"></canvas>
    </div>

    {{-- Pie Chart --}}
    <div class="bg-white shadow rounded-2xl p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Outstanding vs Total Quotation</h2>
      <canvas id="pieChart"></canvas>
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
  new Chart(document.getElementById('barChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: ['Quotation Value', 'Sales Value'],
      datasets: [{
        label: 'Amount (Rp)',
        data: [{{ $totalQuotationValue }}, {{ $totalSalesValue }}],
        backgroundColor: ['#3b82f6', '#8b5cf6'],
        borderRadius: 8,
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  // Pie Chart
  new Chart(document.getElementById('pieChart').getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Outstanding Quotation', 'Other Quotation'],
      datasets: [{
        data: [{{ $outstandingQuotation }}, {{ $totalQuotation - $outstandingQuotation }}],
        backgroundColor: ['#f59e0b', '#10b981'],
        hoverOffset: 6
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
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
