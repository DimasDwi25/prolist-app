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
<div class="max-w-lg mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">🔑 Change Password</h1>
    <p class="text-sm text-gray-500 mb-6">Enter your current password, then create a new one.</p>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-600 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-600 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('account.password.update') }}" class="space-y-5">
        @csrf

        <!-- Current Password -->
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
            <div class="relative">
                <input type="password" name="current_password" id="current_password" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                <button type="button" onclick="togglePassword('current_password')" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                    👁️
                </button>
            </div>
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
            <div class="relative">
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                <button type="button" onclick="togglePassword('password')" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                    👁️
                </button>
            </div>
        </div>

        <!-- Confirm New Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                <button type="button" onclick="togglePassword('password_confirmation')" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                    👁️
                </button>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('account.index') }}" class="text-sm text-gray-500 hover:text-gray-700">⬅ Back</a>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                Save Password
            </button>
        </div>
    </form>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
    }
</script>
@endsection
