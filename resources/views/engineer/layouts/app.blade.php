<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" x-cloak>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Engineer Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 w-64 bg-[#0074A8] text-white shadow-md z-30 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col justify-between">

            <!-- Top Logo -->
            <div>
                <div class="p-4 border-b border-[#005f87] bg-white flex items-center space-x-3">
                    <img src="{{ asset('images/CITASys Logo.jpg') }}" alt="Logo" class="w-40 h-10">
                </div>

                <!-- Navigation -->
                <nav class="mt-4 space-y-1 px-4">
                    <a href="{{ route('engineer.dashboard') }}"
                        class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📊 Dashboard</a>

                    <!-- Project Dropdown -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex justify-between w-full px-4 py-2 rounded hover:bg-[#005f87] transition">
                            <span>🛠 Project</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1 text-sm">
                            <a href="{{ route('engineer.work_order') }}" class="block px-4 py-2 rounded hover:bg-[#005f87]">🛠 Work Order</a>
                            <a href="{{ route('engineer.project') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">🛠 Projects</a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Logout Button (Bottom Sidebar) -->
            <div class="p-4 border-t border-[#005f87]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Overlay (Mobile) -->
        <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
            @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow px-6 py-4 w-full flex justify-between items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-[#0074A8] focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center gap-2 sm:gap-4 ml-auto">
                    <span class="text-[#0074A8] hidden sm:inline">Hi, {{ Auth::user()->name }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                        class="rounded-full w-8 h-8 object-cover" alt="avatar" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    @livewireScripts
    @stack('scripts')
</body>

</html>