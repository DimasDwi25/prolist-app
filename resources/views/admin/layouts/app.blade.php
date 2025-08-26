<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" x-cloak>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    {{-- Tailwind & App --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire & WireUI --}}
    @livewireStyles
    <wireui:scripts />
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 w-64 h-screen bg-[#0074A8] text-white shadow-md z-30 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col justify-between overflow-y-auto">


            <!-- Logo -->
            <div>
                <div class="p-4 border-b border-[#005f87] bg-white flex items-center space-x-3">
                    <img src="{{ asset('images/CITASys Logo.jpg') }}" alt="Logo" class="w-40 h-10">
                </div>

                <!-- Navigation -->
                <nav class="mt-4 space-y-1 px-4">
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📊 Dashboard</a>
                        <!-- Dashboard -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex justify-between w-full px-4 py-2 rounded hover:bg-[#005f87] transition">
                            <span>📊 Choose Dashboard</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1 text-sm">
                             <a href="{{ route('marketing.dashboard') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📊 Marketing Dashboard</a>
                            <a href="#"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">📊 Engineer Dashboard</a>
                        </div>
                    </div>
                    <!-- Manajemen User -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex justify-between w-full px-4 py-2 rounded hover:bg-[#005f87] transition">
                            <span>🛠 Manajemen User</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1 text-sm">
                            <a href="{{ route('admin.role') }}" class="block px-4 py-2 rounded hover:bg-[#005f87]">🔑
                                Roles</a>
                            <a href="{{ route('admin.department') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">🏢 Departments</a>
                            <a href="{{ route('admin.user') }}" class="block px-4 py-2 rounded hover:bg-[#005f87]">👥
                                Users</a>
                        </div>
                    </div>

                    <a href="{{ route('quotation.index') }}"
                            class="block px-4 py-2 rounded hover:bg-[#005f87]">🧾 Quotation</a>
                    
                    <!-- Project -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex justify-between w-full px-4 py-2 rounded hover:bg-[#005f87] transition">
                            <span>🛠 Project</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1 text-sm">
                            <a href="{{ route('supervisor.category') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">📁 Categories</a>
                            <a href="{{ route('status_project') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📁 Status Project</a>
                            <a href="{{ route('supervisor.project') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">🛠 Projects</a>
                        </div>
                    </div>

                    <!-- Quotation -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex justify-between w-full px-4 py-2 rounded hover:bg-[#005f87] transition">
                            <span>📈 Reports</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1 text-sm">
                            <a href="{{ route('supervisor.marketing.report') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">📈 Marketing Reports</a>
                            <a href="{{ route('supervisor.sales.report') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">📈 Sales Reports</a>
                        </div>
                    </div>

                    <a href="{{ route('supervisor.client') }}"
                        class="block px-4 py-2 rounded hover:bg-[#005f87] transition">👥 Client</a>

                    
                </nav>
            </div>
        </div>

        <!-- Overlay (Mobile) -->
        <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
            @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white border-b shadow-sm px-6 py-4 w-full flex items-center justify-between">
                <!-- Sidebar Toggle (Mobile) -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-primary-600 hover:text-primary-700 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="text-xl font-semibold text-primary-700 hidden md:block">
                    Admin Dashboard
                </div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0074A8&color=fff"
                            class="w-9 h-9 rounded-full border-2 border-primary-200 shadow-sm object-cover"
                            alt="Avatar" />
                        <span class="hidden sm:block text-gray-700 text-sm font-medium">
                            👋 Hi, <span class="font-semibold text-primary-700">{{ Auth::user()->name }}</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-600 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 overflow-hidden border border-gray-200">
                        <div class="px-4 py-3 text-sm text-gray-700 border-b">
                            <div class="font-medium">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                        <a href="{{ route('account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">⚙️ Pengaturan
                            Akun</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">🔓
                                Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts
    @stack('scripts')
</body>

</html>