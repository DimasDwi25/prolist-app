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
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-gray-100 space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Project Details</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                        @if($project->statusProject->name === 'Active') bg-green-100 text-green-800
                        @elseif($project->statusProject->name === 'On Hold') bg-yellow-100 text-yellow-800
                        @elseif($project->statusProject->name === 'Completed') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $project->statusProject->name }}
                    </span>
                </div>
                <div class="flex items-center mt-2">
                    <a href="{{ route('supervisor.project') }}" class="text-sm text-blue-600 hover:text-blue-800 transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Projects
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                {{-- Edit Project Button --}}
                <a href="{{ route('project.edit', $project) }}" class="flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Project
                </a>

                {{-- PHC Actions --}}
                @if ($phc)
                    <a href="{{ route('phc.edit', $phc->id) }}" class="flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit PHC
                    </a>
                    
                    <a href="{{ route('phc.show', $phc->id) }}" class="flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View PHC
                    </a>
                @else
                    <a href="{{ route('phc', $project->pn_number) }}" class="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create PHC
                    </a>
                @endif

                {{-- View Logs --}}
                <a href="{{ route('supervisor.projects.logs', $project->pn_number) }}" class="flex items-center gap-1 bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    View Logs
                </a>
            </div>
        </div>

        {{-- PHC Status Alert --}}
        @if ($phc && $phc->status === 'ready')
            <div class="w-full bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">PHC is ready for next steps</p>
                        <p class="mt-1 text-sm text-green-700">All approvals have been completed for this PHC document.</p>
                    </div>
                </div>
            </div>
        @elseif ($phc && $pendingApprovals->isNotEmpty())
            <div class="w-full bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">Pending PHC Approvals</p>
                        <p class="mt-1 text-sm text-yellow-700">Awaiting approval from:</p>
                        <ul class="mt-1 space-y-1">
                            @foreach ($pendingApprovals as $approval)
                                <li class="text-sm text-yellow-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $approval->user->name ?? 'User not found' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabs Navigation --}}
        <div x-data="{ activeTab: 'project' }" class="mt-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'project'" 
                        :class="activeTab === 'project' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Project Information
                    </button>
                    <button @click="activeTab = 'quotation'" 
                        :class="activeTab === 'quotation' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Quotation Details
                    </button>
                    <button @click="activeTab = 'relationships'" 
                        :class="activeTab === 'relationships' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Project Relationships
                    </button>
                    <button @click="activeTab = 'status'" 
                        :class="activeTab === 'status' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Status & Timeline
                    </button>
                </nav>
            </div>

            {{-- Project Information Tab --}}
            <div x-show="activeTab === 'project'" x-cloak class="py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Project Number</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->project_number) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->project_name) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Category</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->category->name ?? null) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mandays (Engineer)</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->mandays_engineer) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mandays (Technician)</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->mandays_technician) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Target Date</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $formatDate($project->target_dates) }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">PO Date</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $formatDate($project->po_date) }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">PO Week</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $project->sales_weeks ?? '—' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">PO Value</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            @isset($project->po_value)
                                Rp {{ number_format(floatval($project->po_value), 0, ',', '.') }}
                            @else
                                -
                            @endisset
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->po_number) }}</p>
                    </div>
                </div>
            </div>

            {{-- Quotation Details Tab --}}
            <div x-show="activeTab === 'quotation'" x-cloak class="py-6">
                @if($project->quotation)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation Number</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->quotation->no_quotation) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->quotation->client->name) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation Value</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">Rp {{ $formatDecimal($project->quotation->quotation_value) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation Date</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $formatDate($project->quotation->quotation_date) }}</p>
                    </div>
  
                </div>
                @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                No quotation information available for this project.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Project Relationships Tab --}}
            <div x-show="activeTab === 'relationships'" x-cloak class="py-6">
                 @if($hasParent || $hasVariants)
                    <div class="space-y-6">
                        {{-- Parent Project Section --}}
                        @if($hasParent)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Parent Project</h3>
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <h4 class="text-xs font-medium text-blue-800 uppercase tracking-wider">Project Number</h4>
                                            <p class="mt-1 text-sm font-medium text-blue-900">{{ $parentProject->project_number }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-medium text-blue-800 uppercase tracking-wider">Project Name</h4>
                                            <p class="mt-1 text-sm font-medium text-blue-900">{{ $parentProject->project_name }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-medium text-blue-800 uppercase tracking-wider">Status</h4>
                                            <p class="mt-1 text-sm font-medium text-blue-900">{{ $parentProject->statusProject->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('supervisor.project.show', $parentProject) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                            View Parent Project
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Child Projects Section --}}
                        @if($hasVariants)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Variant Orders</h3>
                                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($childProjects as $child)
                                        <li>
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-indigo-600 truncate">{{ $child->project_number }}</p>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @if($child->statusProject->name === 'Active') bg-green-100 text-green-800
                                                            @elseif($child->statusProject->name === 'On Hold') bg-yellow-100 text-yellow-800
                                                            @elseif($child->statusProject->name === 'Completed') bg-blue-100 text-blue-800
                                                            @else bg-gray-100 text-gray-800 @endif">
                                                            {{ $child->statusProject->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                            </svg>
                                                            Target: {{ $formatDate($child->target_dates) }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                        </svg>
                                                        Created: {{ $formatDate($child->created_at) }}
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('supervisor.project.show', $child) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                                        View Details
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No project relationships</h3>
                        <p class="mt-1 text-sm text-gray-500">This project doesn't have any parent or variant projects associated with it.</p>
                    </div>
                @endif
            </div>

            {{-- Status & Timeline Tab --}}
            <div x-show="activeTab === 'status'" x-cloak class="py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Current Status</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $display($project->statusProject->name) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $formatDate($project->created_at) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $formatDate($project->updated_at) }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Target Completion</h3>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $formatDate($project->target_dates) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity Section --}}
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                <div class="flex space-x-3">
                    {{-- Tombol Buka Modal --}}
                    <button type="button"
                        onclick="Livewire.dispatch('openLogModal')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm w-full md:w-auto">
                        + Tambah Log
                    </button>

                    {{-- Include Livewire Component --}}
                    @livewire('log.log-modal', ['projectId' => $project->pn_number])

                </div>
                <a href="{{ route('supervisor.projects.logs', $project->pn_number) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    View all activity
                </a>
            </div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                @livewire('log.log-table', ['projectId' => $project->pn_number, 'perPage' => 5])
            </div>
        </div>
    </div>
@endsection