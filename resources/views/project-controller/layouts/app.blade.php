<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" x-cloak>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>SysPro</title>

    {{-- Tailwind & App Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Livewire & WireUI --}}
    @livewireStyles
    <wireui:scripts />

    {{-- jQuery & Select2 & Chart.js --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full min-h-screen flex flex-col bg-gray-100 font-sans leading-normal tracking-normal overflow-x-hidden">
<div class="flex flex-1 overflow-hidden">
    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     class="fixed inset-y-0 left-0 w-48 md:w-52 bg-[#0074A8] text-white shadow-md z-30 transform transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col justify-between overflow-y-auto text-sm h-screen">

        <!-- Logo -->
        <div>
            <div class="p-3 border-b border-[#005f87] bg-white flex items-center justify-center">
                <img src="{{ asset('images/CITASys Logo.jpg') }}" alt="Logo" class="w-32 h-8 object-contain">
            </div>

            <!-- Navigation -->
            <nav class="mt-3 space-y-1 px-2">
                <a href="{{ route('engineer.dashboard') }}" class="block px-3 py-2 rounded hover:bg-[#005f87] transition">📊 Dashboard</a>

                <!-- Project -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex justify-between w-full px-3 py-2 rounded hover:bg-[#005f87] transition">
                        <span>🛠 Project</span>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none"
                             stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="pl-3 mt-1 space-y-1 text-xs">
                        <a href="{{ route('engineer.work_order') }}" class="block px-3 py-2 rounded hover:bg-[#005f87]">📁 Work Order</a>
                        <a href="{{ route('engineer.project.index') }}" class="block px-3 py-2 rounded hover:bg-[#005f87]">🛠 Projects</a>
                    </div>
                </div>

                <a href="{{ route('tasks') }}" class="block px-3 py-2 rounded hover:bg-[#005f87] transition">📁 Task</a>
                <a href="{{ route('scope_of_work') }}" class="block px-3 py-2 rounded hover:bg-[#005f87] transition">📁 Scope Of Work</a>
            </nav>
        </div>
    </div>

    <!-- Overlay (Mobile) -->
    <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
         @click="sidebarOpen = false" x-transition.opacity></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden md:ml-52">
        <!-- Header -->
        <header class="bg-white border-b shadow-sm px-4 py-2 flex items-center justify-between">
            <!-- Sidebar Toggle (Mobile) -->
            <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-primary-600 hover:text-primary-700 transition focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="text-base md:text-lg font-semibold text-primary-700 hidden md:block"></div>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0074A8&color=fff"
                         class="w-8 h-8 rounded-full border border-primary-200 object-cover" alt="Avatar"/>
                    <span class="hidden sm:block text-gray-700 text-xs font-medium">
                        👋 Hi, <span class="font-semibold text-primary-700">{{ Auth::user()->name }}</span>
                    </span>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-44 bg-white rounded-md shadow-lg z-50 overflow-hidden border border-gray-200 text-sm">
                    <div class="px-3 py-2 text-gray-700 border-b">
                        <div class="font-medium">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('account.index') }}" class="block px-3 py-2 hover:bg-gray-100">⚙️ Pengaturan Akun</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 hover:text-red-700">🔓 Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-3 md:p-4">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
        
    </div>
</div>

{{-- Livewire Scripts --}}
@livewireScripts
@stack('scripts')

<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    main {
        min-height: calc(100vh - 80px); /* Sesuaikan tinggi header/footer */
    }
</style>
</body>


</html>
