<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'E-Budgeting System') }} — @yield('title', 'Dashboard')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Serif+Display&display=swap" rel="stylesheet" />

    {{-- Tailwind v4 via CDN (ganti dengan Vite jika sudah setup) --}}
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>

    @stack('styles')
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen">

    {{-- Mobile Overlay --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm hidden lg:hidden transition-opacity"
         onclick="closeSidebar()">
    </div>

    <div class="flex min-h-screen">

        {{-- ═══════════════════════════════════════════════
             SIDEBAR
        ═══════════════════════════════════════════════ --}}
        <aside id="sidebar"
               class="sidebar-grain flex flex-col w-64 h-[100dvh] bg-slate-900 z-40
                      fixed lg:sticky top-0
                      -translate-x-full lg:translate-x-0
                      shrink-0 overflow-y-auto self-start">

            {{-- Logo / Brand --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.06]">
                <div class="w-10 h-10 rounded-md bg-white flex items-center justify-center shrink-0 p-1">
                    <img src="{{ asset('images/ippi_logo.jpg') }}" alt="IPPI Logo" class="w-full h-full object-contain" />
                </div>
                <div class="overflow-hidden">
                    <p class="text-white font-semibold text-sm leading-tight tracking-tight">E-Budgeting System</p>
                    <p class="text-slate-400 text-[10px] leading-tight mt-0.5">PT INTI PANTJA PRESS INDUSTRI</p>
                </div>

                {{-- Close button mobile --}}
                <button onclick="closeSidebar()"
                        class="ml-auto lg:hidden p-1 rounded-md text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- User Info --}}
            <div class="px-4 py-3 mx-3 mt-3 rounded-xl bg-white/[0.05] border border-white/[0.06]">
                <p class="text-white text-sm font-medium leading-tight truncate">{{ Auth::user()->name }}</p>
                <p class="text-slate-400 text-[11px] mt-0.5 truncate">{{ Auth::user()->department?->dept_name ?? 'Tanpa Departemen' }}</p>
                <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium
                    {{ match(Auth::user()->role) {
                        'ka_div', 'ka_div_acc'            => 'bg-indigo-500/20 text-indigo-300',
                        'ka_dept', 'ka_dept_acc'          => 'bg-sky-500/20 text-sky-300',
                        'ka_sie'                          => 'bg-teal-500/20 text-teal-300',
                        'accounting'                      => 'bg-emerald-500/20 text-emerald-300',
                        'fin_dir', 'man_dir', 'pres_dir'  => 'bg-amber-500/20 text-amber-300',
                        'superadmin'                      => 'bg-violet-500/20 text-violet-300',
                        default                           => 'bg-slate-500/20 text-slate-400',
                    } }}">
                    {{ match(Auth::user()->role) {
                        'staff'       => 'Staff',
                        'ka_sie'      => 'Kepala Seksi',
                        'ka_dept'     => 'Ka. Departemen',
                        'ka_div'      => 'Ka. Divisi',
                        'accounting'  => 'Accounting',
                        'ka_dept_acc' => 'Ka. Dept Accounting',
                        'ka_div_acc'  => 'Ka. Div Accounting',
                        'fin_dir'     => 'Finance Director',
                        'man_dir'     => 'Manufacture Director',
                        'pres_dir'    => 'President Director',
                        'superadmin'  => 'Superadmin',
                        default       => 'Unknown Role',
                    } }}
                </span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Menu Utama</p>

                {{-- Dashboard --}}
                <a href="{{ Auth::user()->isDirector() ? route('director.dashboard') : route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('dashboard') || request()->routeIs('director.dashboard') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                    Dashboard
                </a>

                @if(Auth::user()->isDirector())
                {{-- ── DIRECTOR MENU ── --}}
                <p class="px-3 pb-1 pt-3 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Monitoring</p>

                <a href="{{ route('director.departments.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('director.departments.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21"/>
                    </svg>
                    Departemen
                </a>

                <a href="{{ route('tracking.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('tracking.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                    Tracking &amp; Approval
                </a>

                @else
                {{-- ── STAFF / KA. DEPT MENU ── --}}
                <a href="{{ route('pengajuan.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('pengajuan.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    Menu Pengajuan
                </a>

                <a href="{{ route('tracking.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('tracking.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                    Tracking Approval
                </a>

                @if(Auth::user()->isKaDept())
                <a href="{{ route('budget.upload.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('budget.upload.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:text-white hover:bg-white/[0.07]' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    Upload Budget Excel
                </a>
                @endif
                @endif
            </nav>

            {{-- Logout --}}
            <div class="px-3 pb-4 border-t border-white/[0.06] pt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-150">
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
            <header class="sticky top-0 z-20 flex items-center gap-3 px-4 sm:px-6 h-14 bg-white/80 backdrop-blur-md border-b border-slate-100">
                {{-- Hamburger --}}
                <button onclick="toggleSidebar()"
                        class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-400 leading-tight">@yield('page-subtitle', '')</p>
                </div>

                {{-- Breadcrumb Desktop --}}
                <nav class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400">
                    <span>E-Budgeting System</span>
                    <span>/</span>
                    <span class="text-slate-600 font-medium">@yield('page-title', 'Dashboard')</span>
                </nav>

                {{-- Real-time Clock --}}
                <div class="flex items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 ml-auto sm:ml-3">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="realtime-clock" class="text-[10px] sm:text-xs font-mono font-medium text-slate-600">00:00:00</span>
                </div>

                {{-- Profile Avatar --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 pl-3 border-l border-slate-100 ml-2 hover:opacity-80 transition-opacity">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 text-xs font-semibold">
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
                E-Budgeting System &copy; {{ date('Y') }} — v1.0.0
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
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