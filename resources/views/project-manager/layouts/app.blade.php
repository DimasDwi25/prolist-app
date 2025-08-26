<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" x-cloak>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>SysPro</title>

    {{-- Tailwind & App Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Livewire & WireUI --}}
    @livewireStyles
    <wireui:scripts />
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 w-64 flex-shrink-0 bg-[#0074A8] text-white shadow-md z-30 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col justify-between"
        >
            <div>
                <div class="p-4 border-b border-[#005f87] bg-white flex items-center space-x-3">
                    <img src="{{ asset('images/CITASys Logo.jpg') }}" alt="Logo" class="w-40 h-10">
                </div>

                <nav class="mt-4 space-y-1 px-4">
                    <a href="{{ route('engineer.dashboard') }}"
                        class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📊 Dashboard</a>

                    <!-- Project Dropdown -->
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
                            <a href="{{ route('engineer.work_order') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">📁 Work Order</a>
                            <a href="{{ route('engineer.project.index') }}"
                                class="block px-4 py-2 rounded hover:bg-[#005f87]">🛠 Projects</a>
                        </div>
                    </div>

                    <a href="{{ route('tasks') }}" class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📁
                        Task</a>
                    <a href="{{ route('scope_of_work') }}"
                        class="block px-4 py-2 rounded hover:bg-[#005f87] transition">📁
                        Scope Of Work</a>
                </nav>
            </div>
        </div>

        <!-- Overlay (Mobile) -->
        <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
            @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b shadow-sm px-6 py-4 w-full flex items-center justify-between">
                <!-- Sidebar Toggle (Mobile Only) -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-primary-600 hover:text-primary-700 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="text-xl font-semibold text-primary-700 hidden md:block">
                    {{-- Judul Halaman atau Logo di tengah --}}
                </div>

                <!-- Right Section -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    @livewire('notification-bell')

                    <!-- Profile Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0074A8&color=fff"
                                class="w-9 h-9 rounded-full border-2 border-primary-200 shadow-sm object-cover"
                                alt="Avatar" />
                            <span class="hidden sm:block text-gray-700 text-sm font-medium">
                                👋 Hi, <span class="font-semibold text-primary-700">{{ Auth::user()->name }}</span>
                            </span>
                            <svg class="w-4 h-4 text-gray-600 ml-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 overflow-hidden border border-gray-200">
                            <div class="px-4 py-3 text-sm text-gray-700 border-b">
                                <div class="font-medium">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">⚙️ Pengaturan
                                Akun</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">🔓
                                    Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 w-full overflow-auto p-4 md:p-6">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </div>

    {{-- Success Alert --}}
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

    {{-- jQuery & Select2 & SweetAlert --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Livewire & Stack --}}
    @livewireScripts
    @stack('scripts')

    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.js-example-basic-single').select2({
                placeholder: '-- Select Client --',
                allowClear: true,
                minimumResultsForSearch: 5,
                width: 'resolve',
                dropdownAutoWidth: true,
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Pusher/Echo
            // Listener untuk channel public PHC
            window.Echo.channel("phc.notifications").listen(".phc.created", (e) => {
                const userMeta = document.head.querySelector('meta[name="user-id"]');
                if (userMeta) {
                    const userId = parseInt(userMeta.content, 10);

                    // Hanya update untuk user yang jadi target
                    if (e.user_ids.includes(userId)) {
                        // Panggil Livewire biar icon bell refresh
                        Livewire.emit("refreshNotifications");

                        // Optional: bunyikan suara & alert
                        const audio = new Audio('{{ asset("sounds/notification.mp3") }}');
                        audio.play().catch(() => { });
                        console.log("New PHC Notification:", e.message);
                    }
                }
            });

        });
    </script>
    {{-- Global Loading Indicator (centered) --}}
    <div wire:loading.delay.shortest
        class="fixed inset-0 bg-white bg-opacity-60 flex items-center justify-center z-[9999]">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent"></div>
    </div>

</body>

</html>