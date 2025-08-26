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
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp
@extends($layout)

@section('content')
<div class="max-w-lg mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">🔒 Change PIN</h1>
    <p class="text-sm text-gray-500 mb-6">Enter your new PIN (6 digits).</p>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-600 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-600 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('account.pin.update') }}" class="space-y-5">
        @csrf

        <!-- New PIN -->
        <div>
            <label for="pin" class="block text-sm font-medium text-gray-700">New PIN</label>
            <div class="relative">
                <input type="password" name="pin" id="pin" required minlength="6" maxlength="6"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 pr-10"
                    placeholder="******">
                <button type="button" onclick="togglePin('pin')" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                    👁️
                </button>
            </div>
        </div>

        <!-- Confirm New PIN -->
        <div>
            <label for="pin_confirmation" class="block text-sm font-medium text-gray-700">Confirm New PIN</label>
            <div class="relative">
                <input type="password" name="pin_confirmation" id="pin_confirmation" required minlength="6" maxlength="6"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 pr-10"
                    placeholder="******">
                <button type="button" onclick="togglePin('pin_confirmation')" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                    👁️
                </button>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('account.index') }}" class="text-sm text-gray-500 hover:text-gray-700">⬅ Back</a>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-purple-600 text-white font-medium hover:bg-purple-700 transition">
                Save PIN
            </button>
        </div>
    </form>
</div>

<script>
    function togglePin(id) {
        const input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
    }
</script>
@endsection
