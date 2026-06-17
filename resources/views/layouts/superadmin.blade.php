<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'E-Budgeting System') }} — @yield('title', 'Superadmin')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Serif+Display&display=swap" rel="stylesheet" />

    {{-- Tailwind v4 via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['"DM Sans"', 'sans-serif'],
                        display: ['"DM Serif Display"', 'serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#1e1b4b',
                        },
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.04)',
                        'card-hover': '0 4px 12px 0 rgb(0 0 0 / 0.08), 0 2px 4px -1px rgb(0 0 0 / 0.06)',
                    },
                }
            }
        }
    </script>

    <style>
        /* Grain texture overlay untuk sidebar */
        .sidebar-grain::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            border-radius: inherit;
        }

        /* Smooth sidebar transition */
        #sidebar { transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s cubic-bezier(0.4, 0, 0.2, 1); }
        #main-content { transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

        /* Page load animation */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-page { animation: fadeSlideUp 0.4s ease both; }
        .animate-page-delay-1 { animation: fadeSlideUp 0.4s ease 0.08s both; }
        .animate-page-delay-2 { animation: fadeSlideUp 0.4s ease 0.16s both; }
        .animate-page-delay-3 { animation: fadeSlideUp 0.4s ease 0.24s both; }
        .animate-page-delay-4 { animation: fadeSlideUp 0.4s ease 0.32s both; }

        /* Counter animation */
        @keyframes countUp {
            from { opacity: 0; transform: scale(0.8); }
            to   { opacity: 1; transform: scale(1); }
        }
        .animate-count { animation: countUp 0.5s ease both; }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased">

    {{-- Mobile Overlay --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm hidden"
         onclick="closeSidebar()">
    </div>

    <div class="flex min-h-screen">

        {{-- ═══════════════════════════════════════════════
             SIDEBAR — Superadmin (Dark Indigo/Purple)
        ═══════════════════════════════════════════════ --}}
        <aside id="sidebar"
               class="sidebar-grain flex flex-col w-64 h-[100dvh] z-50
                      fixed top-0 left-0
                      -translate-x-full lg:translate-x-0
                      shrink-0 overflow-y-auto overflow-x-hidden
                      lg:sticky"
               style="background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);">

            {{-- Logo / Brand --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.08]">
                <div class="w-10 h-10 rounded-md bg-white flex items-center justify-center shrink-0 p-1 shadow-lg shadow-violet-500/30">
                    <img src="{{ asset('images/ippi_logo.jpg') }}" alt="IPPI Logo" class="w-full h-full object-contain" />
                </div>
                <div class="overflow-hidden">
                    <p class="text-white font-semibold text-sm leading-tight tracking-tight">E-Budgeting System</p>
                    <p class="text-violet-300/60 text-[10px] leading-tight mt-0.5">Superadmin Panel</p>
                </div>

                {{-- Close button mobile --}}
                <button onclick="closeSidebar()"
                        class="ml-auto lg:hidden p-1 rounded-md text-violet-300 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Admin Info --}}
            <div class="px-4 py-3 mx-3 mt-3 rounded-xl bg-white/[0.06] border border-white/[0.08]">
                <p class="text-white text-sm font-medium leading-tight truncate">{{ Auth::user()->name }}</p>
                <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold
                    bg-gradient-to-r from-violet-500/30 to-indigo-500/30 text-violet-200 border border-violet-400/20">
                    ⚡ SUPERADMIN
                </span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-violet-400/50">Overview</p>

                <a href="{{ route('superadmin.dashboard') }}" onclick="closeSidebarMobile()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('superadmin.dashboard') ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/25' : 'text-violet-200/70 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                    Dashboard
                </a>

                <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-violet-400/50">Kelola</p>

                <a href="{{ route('superadmin.users.index') }}" onclick="closeSidebarMobile()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('superadmin.users.*') ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/25' : 'text-violet-200/70 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    Kelola User
                </a>

                <a href="{{ route('superadmin.departments.index') }}" onclick="closeSidebarMobile()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('superadmin.departments.*') ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/25' : 'text-violet-200/70 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                    </svg>
                    Departemen
                </a>

                <a href="{{ route('superadmin.cost-centers.index') }}" onclick="closeSidebarMobile()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('superadmin.cost-centers.*') ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/25' : 'text-violet-200/70 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z"/>
                    </svg>
                    Cost Center
                </a>

                <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-violet-400/50">Monitoring</p>

                <a href="{{ route('superadmin.budget.index') }}" onclick="closeSidebarMobile()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('superadmin.budget.*') ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/25' : 'text-violet-200/70 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
                    </svg>
                    Budget Overview
                </a>

                <a href="{{ route('superadmin.audit.index') }}" onclick="closeSidebarMobile()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('superadmin.audit.*') ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/25' : 'text-violet-200/70 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    Audit Log
                </a>
            </nav>

            {{-- Logout --}}
            <div class="px-3 pb-4 border-t border-white/[0.08] pt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-violet-200/60 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-150">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ═══════════════════════════════════════════════
             MAIN CONTENT
        ═══════════════════════════════════════════════ --}}
        <div id="main-content" class="flex-1 flex flex-col min-w-0 min-h-screen">

            {{-- Top Bar --}}
            <header class="sticky top-0 z-30 flex items-center gap-3 px-4 sm:px-6 h-14 bg-white/80 backdrop-blur-md border-b border-slate-100">
                {{-- Hamburger --}}
                <button onclick="toggleSidebar()"
                        class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-slate-800">@yield('page-title', 'Superadmin')</h1>
                    <p class="text-xs text-slate-400 leading-tight">@yield('page-subtitle', '')</p>
                </div>

                {{-- Breadcrumb Desktop --}}
                <nav class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400">
                    <span>E-Budgeting System</span>
                    <span>/</span>
                    <span class="text-violet-600 font-medium">Superadmin</span>
                    <span>/</span>
                    <span class="text-slate-600 font-medium">@yield('page-title', 'Dashboard')</span>
                </nav>

                {{-- Real-time Clock --}}
                <div class="hidden sm:flex items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-1.5 rounded-lg bg-slate-900/5 border border-slate-900/10 ml-auto sm:ml-3">
                    <svg class="w-3.5 h-3.5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="realtime-clock" class="text-[10px] sm:text-xs font-mono font-medium text-slate-600">00:00:00</span>
                </div>

                {{-- Admin Badge --}}
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 pl-3 border-l border-slate-100 ml-2 hover:opacity-80 transition-opacity">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-md shadow-violet-500/20">
                        <span class="text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                </a>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mx-4 sm:mx-6 mt-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 animate-page">
                <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="mx-4 sm:mx-6 mt-4 flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 animate-page">
                <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                </svg>
                {{ session('warning') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mx-4 sm:mx-6 mt-4 flex items-start gap-3 px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700 animate-page">
                <svg class="w-4 h-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <ul class="space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 px-4 sm:px-6 py-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="px-6 py-3 border-t border-slate-100 text-center text-xs text-slate-400">
                E-Budgeting System &copy; {{ date('Y') }} — Superadmin Panel v1.0.0
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isHidden = sidebar.classList.contains('-translate-x-full');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.style.overflow = isHidden ? 'hidden' : '';
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }
        // Only close on mobile (< 1024px)
        function closeSidebarMobile() {
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        }

        // Real-time Clock
        function updateClock() {
            const now = new Date();
            const options = { 
                timeZone: 'Asia/Jakarta', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit', 
                hour12: false 
            };
            const timeString = new Intl.DateTimeFormat('en-GB', options).format(now);
            const clockElement = document.getElementById('realtime-clock');
            if (clockElement) {
                clockElement.textContent = timeString + ' WIB';
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    @stack('scripts')
</body>
</html>
