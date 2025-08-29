@php
    $roleLayouts = [
        'super_admin'          => 'admin.layouts.app',
        'marketing_director'   => 'marketing-director.layouts.app',
        'supervisor marketing' => 'supervisor.layouts.app',
        'manager_marketing'    => 'supervisor.layouts.app',
        'sales_supervisor'     => 'supervisor.layouts.app',
        'marketing_admin'      => 'supervisor.layouts.app',
        'engineering_director' => 'engineering_director.layouts.app',
        'project controller'     => 'project-controller.layouts.app',
        'engineer'     => 'engineer.layouts.app',
        'engineering_manager'         => 'project-manager.layouts.app',
        'marketing_estimator' => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)
@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">👤 Account Information</h1>
            <p class="text-sm text-gray-500">Manage your profile, change your password, and update your PIN.</p>
        </div>
        <div class="h-14 w-14 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
            {{ strtoupper(substr(Auth::user()->name,0,2)) }}
        </div>
    </div>

    <!-- Account Info Card -->
    <div class="bg-white shadow rounded-2xl p-6 border border-gray-100 mb-8">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-800">{{ Auth::user()->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-800">{{ Auth::user()->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Role</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-800">{{ Auth::user()->role->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-800">{{ Auth::user()->created_at->format('F d, Y') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-2xl p-6 flex flex-col items-start border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-2">🔑 Change Password</h2>
            <p class="text-sm text-gray-500 mb-4">Make sure your new password is strong and secure.</p>
            <a href="{{ route('account.password.edit') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition duration-200">
                Update Password
            </a>
        </div>

        <div class="bg-white shadow rounded-2xl p-6 flex flex-col items-start border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-2">🔒 Change PIN</h2>
            <p class="text-sm text-gray-500 mb-4">Use a new PIN for additional account security.</p>
            <a href="{{ route('account.pin.edit') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition duration-200">
                Update PIN
            </a>
        </div>
    </div>
</div>
@endsection
