@php
    $roleLayouts = [
        'super_admin'          => 'admin.layouts.app',
        'marketing_director'   => 'marketing-director.layouts.app',
        'supervisor marketing' => 'supervisor.layouts.app',
        'manager_marketing'    => 'supervisor.layouts.app',
        'sales_supervisor'     => 'supervisor.layouts.app',
        'marketing_admin'      => 'supervisor.layouts.app',
        'engineering_director' => 'engineering_director.layouts.app',
        'marketing_estimator'  => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 rounded-2xl shadow-lg p-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7h18M3 12h18M3 17h18"/>
            </svg>
            Client Management
        </h1>

        <a href="{{ route('client.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm shadow-md transition-all duration-200 ease-in-out">
            + Add Client
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <livewire:client-table />
    </div>
</div>
@endsection

@push('styles')
<style>
/* General table styling */
table {
    border-collapse: collapse;
    width: 100%;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

thead th {
    background-color: #f9fafb;
    color: #1f2937;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
    text-align: left;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #e5e7eb;
}

tbody tr {
    transition: background 0.25s ease, transform 0.2s ease;
    cursor: pointer;
}

tbody tr:hover {
    background-color: #eff6ff;
    transform: translateY(-1px);
}

td {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

/* Truncate text & tooltip */
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 250px;
    position: relative;
}

.tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    display: none;
    white-space: pre-wrap;
    background: rgba(31, 41, 55, 0.9);
    color: #fff;
    padding: 6px 10px;
    border-radius: 6px;
    top: 120%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
    min-width: 180px;
    font-size: 0.85rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.tooltip:hover::after {
    display: block;
    animation: fadeIn 0.2s ease-out forwards;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
}

/* Horizontal scroll if table too wide */
.overflow-x-auto {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Optional: responsive for mobile */
@media (max-width: 768px) {
    td, th {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
    .truncate {
        max-width: 120px;
    }
}

/* Optional: smooth hover for buttons in table */
a, button {
    transition: all 0.2s ease-in-out;
}

a:hover, button:hover {
    transform: translateY(-1px);
}
</style>
@endpush
