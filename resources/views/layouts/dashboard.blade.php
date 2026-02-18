<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1e293b">
    <title>Siaga App</title>

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.svg') }}">
    <link rel="icon" href="{{ asset('icons/icon-192x192.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#1e2330] text-white flex-shrink-0 hidden md:flex flex-col transition-all duration-300">
            <div class="p-6">
                <h1 class="text-xl font-bold tracking-wider">SIAGA APP</h1>
            </div>

            <nav class="flex-1 px-4 space-y-2 overflow-y-auto scrollbar-hide">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-[#333b4d] text-white shadow-sm' : 'text-slate-400 hover:bg-[#2a303f] hover:text-white' }} rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-emerald-400' : '' }}"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('personels.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('personels.*') ? 'bg-[#333b4d] text-white shadow-sm' : 'text-slate-400 hover:bg-[#2a303f] hover:text-white' }} rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-3 {{ request()->routeIs('personels.*') ? 'text-emerald-400' : '' }}"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                    <span class="font-medium">Personel</span>
                </a>

                <a href="{{ route('reports.attendance') }}"
                    class="flex items-center px-4 py-3 text-slate-400 hover:bg-[#2a303f] hover:text-white rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Laporan</span>
                </a>

                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('settings.*') ? 'bg-[#333b4d] text-white shadow-sm' : 'text-slate-400 hover:bg-[#2a303f] hover:text-white' }} rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-3 {{ request()->routeIs('settings.*') ? 'text-emerald-400' : '' }}"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Pengaturan</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Mobile Header -->
            <header class="md:hidden bg-white shadow-sm flex items-center justify-between p-4 z-20 flex-shrink-0">
                <button type="button" id="mobileMenuBtn" class="text-slate-700 hover:text-green-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-lg font-bold text-slate-800">SIAGA APP</h1>
                <div
                    class="w-8 h-8 bg-gradient-to-br from-green-700 to-green-800 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                    {{ strtoupper(substr(Auth::guard('personel')->user()->name, 0, 2)) }}
                </div>
            </header>

            <!-- Mobile Page Title & Date -->
            <div class="md:hidden bg-white/50 backdrop-blur-sm px-4 py-3 border-b border-slate-200 flex-shrink-0">
                <h2 class="text-lg font-bold text-slate-800">@yield('title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div id="mobileSidebarOverlay" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden"></div>

            <!-- Mobile Sidebar -->
            <aside id="mobileSidebar"
                class="fixed top-0 left-0 h-full w-64 bg-[#1e2330] text-white transform -translate-x-full transition-transform duration-300 z-50 md:hidden overflow-y-auto">
                <div class="p-6 flex items-center justify-between flex-shrink-0">
                    <h1 class="text-xl font-bold tracking-wider">SIAGA APP</h1>
                    <button type="button" id="closeSidebarBtn"
                        class="text-white hover:text-green-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="px-4 space-y-2 overflow-y-auto scrollbar-hide" style="max-height: calc(100vh - 200px);">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-[#333b4d] text-white shadow-sm' : 'text-slate-400 hover:bg-[#2a303f] hover:text-white' }} rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-emerald-400' : '' }}"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('personels.index') }}"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('personels.*') ? 'bg-[#333b4d] text-white shadow-sm' : 'text-slate-400 hover:bg-[#2a303f] hover:text-white' }} rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-3 {{ request()->routeIs('personels.*') ? 'text-emerald-400' : '' }}"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                        <span class="font-medium">Personel</span>
                    </a>

                    <a href="#"
                        class="flex items-center px-4 py-3 text-slate-400 hover:bg-[#2a303f] hover:text-white rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Laporan</span>
                    </a>

                    <a href="{{ route('settings.index') }}"
                        class="flex items-center px-4 py-3 {{ request()->routeIs('settings.*') ? 'bg-[#333b4d] text-white shadow-sm' : 'text-slate-400 hover:bg-[#2a303f] hover:text-white' }} rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-3 {{ request()->routeIs('settings.*') ? 'text-emerald-400' : '' }}"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Pengaturan</span>

                    </a>
                </nav>

                <!-- Mobile User Profile -->
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-[#171b26] border-t border-slate-700">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-green-700 to-green-800 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(Auth::guard('personel')->user()->name, 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-white">{{ Auth::guard('personel')->user()->name }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ Auth::guard('personel')->user()->role->name ?? 'Personel' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="cursor-pointer w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Topbar (Desktop) -->
            <header class="hidden md:flex items-center justify-between bg-white/50 backdrop-blur-sm px-8 py-4 z-10">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">@yield('title', 'Dashboard')</h1>
                    <p class="text-sm text-slate-500">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
                <div class="flex items-center space-x-6">
                    <!-- User Profile & Logout -->
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ Auth::guard('personel')->user()->name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ Auth::guard('personel')->user()->role->name ?? 'Personel' }}</p>
                        </div>
                        <div class="relative">
                            <button onclick="document.getElementById('profileMenu').classList.toggle('hidden')"
                                class="cursor-pointer flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                                <div
                                    class="w-9 h-9 bg-gradient-to-br from-green-700 to-green-800 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::guard('personel')->user()->name, 0, 2)) }}
                                </div>
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="profileMenu"
                                class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ Auth::guard('personel')->user()->name }}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ Auth::guard('personel')->user()->email }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ Auth::guard('personel')->user()->rank }}
                                        - {{ Auth::guard('personel')->user()->position }}</p>
                                </div>
                                <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                    @csrf
                                    <button type="submit"
                                        class="cursor-pointer w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Scroll Area -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyD3-_HRKwkyedciqw3gSQgU-h7dEbXasx8",
            authDomain: "poltekadsiaga.firebaseapp.com",
            projectId: "poltekadsiaga",
            storageBucket: "poltekadsiaga.firebasestorage.app",
            messagingSenderId: "971088041031",
            appId: "1:971088041031:web:9268e5176456d0b08a4d62"
        };


        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // Handle foreground messages
        onMessage(messaging, (payload) => {
            console.log('Message received in foreground:', payload);

            // Dispatch event for components to listen to
            const event = new CustomEvent('siaga-notification', {
                detail: payload
            });
            window.dispatchEvent(event);

            // Optional: You could also show a toast here if you have a toast library
            // For now, we'll let the active page handle the data update
        });

        async function requestPermission(reg) {
            console.log('Requesting permission...');
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    console.log('Notification permission granted.');

                    const token = await getToken(messaging, {
                        serviceWorkerRegistration: reg
                    });

                    if (token) {
                        console.log('FCM Token:', token);
                        await fetch('{{ route('fcm.subscribe') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                token: token
                            })
                        });
                        console.log('Token sent to server.');
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                } else {
                    console.log('Unable to get permission to notify.');
                }
            } catch (error) {
                console.log('An error occurred while retrieving token. ', error);
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        console.log('Service worker registered.', reg);
                        requestPermission(reg);
                    })
                    .catch((err) => console.log('Service worker registration failed:', err));
            });
        }

        function toggleProfileMenu() {
            const menu = document.getElementById('profileMenu');
            menu.classList.toggle('hidden');
        }


        document.addEventListener('click', function(event) {
            const menu = document.getElementById('profileMenu');
            const button = event.target.closest('button');

            if (button && button.onclick && button.onclick.toString().includes('profileMenu')) {
                return;
            }

            if (menu && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');

            if (sidebar && overlay) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            const overlay = document.getElementById('mobileSidebarOverlay');
            const profileMenuBtn = document.getElementById('profileMenuBtn'); // Get the profile menu button

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', toggleMobileSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', toggleMobileSidebar);
            }

            if (profileMenuBtn) {
                profileMenuBtn.addEventListener('click', toggleProfileMenu);
            }
        });
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scripts')
</body>

</html>
