<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'NaijaProps') . ' – Nigerian Property Listing & Trading System')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Immediate Theme Restoration (Prevents Flash of Unstyled Theme) -->
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('naijaprops_theme') || localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
            document.documentElement.classList.add('js-scroll-anim');
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem;
        }
        body { font-family: 'Inter', sans-serif; }
        ::selection {
            background-color: #1e40af;
            color: #ffffff;
        }
        html.dark ::selection {
            background-color: #3b82f6;
            color: #0f172a;
        }

        /* Global Dark Theme Adaptations for Cards, Tables, Surfaces & Forms */
        html.dark body {
            background-color: #0f172a;
            color: #f1f5f9;
        }
        html.dark .bg-white {
            background-color: #1e293b !important;
        }
        html.dark .bg-white\/95,
        html.dark .bg-white\/90,
        html.dark .bg-white\/80 {
            background-color: rgba(15, 23, 42, 0.92) !important;
            color: #f1f5f9 !important;
        }
        html.dark .text-blue-950 {
            color: #e2e8f0 !important;
        }
        html.dark .bg-slate-50 {
            background-color: #0f172a !important;
        }
        html.dark .bg-slate-100 {
            background-color: #334155 !important;
        }
        html.dark .text-slate-900 {
            color: #f8fafc !important;
        }
        html.dark .text-slate-800 {
            color: #f1f5f9 !important;
        }
        html.dark .text-slate-700 {
            color: #cbd5e1 !important;
        }
        html.dark .text-slate-600 {
            color: #94a3b8 !important;
        }
        html.dark .text-slate-500 {
            color: #94a3b8 !important;
        }
        html.dark .text-blue-900 {
            color: #93c5fd !important;
        }
        html.dark .text-blue-800,
        html.dark .text-blue-700 {
            color: #60a5fa !important;
        }
        html.dark .bg-blue-50,
        html.dark [class*="bg-blue-50/"] {
            background-color: rgba(30, 58, 138, 0.35) !important;
        }
        html.dark .bg-blue-100 {
            background-color: rgba(30, 64, 175, 0.45) !important;
        }
        html.dark .border-blue-100,
        html.dark .border-blue-200 {
            border-color: rgba(59, 130, 246, 0.35) !important;
        }
        html.dark .bg-emerald-50,
        html.dark [class*="bg-emerald-50/"] {
            background-color: rgba(6, 78, 59, 0.35) !important;
        }
        html.dark .bg-emerald-100 {
            background-color: rgba(6, 95, 70, 0.45) !important;
        }
        html.dark .text-emerald-900,
        html.dark .text-emerald-800,
        html.dark .text-emerald-700 {
            color: #6ee7b7 !important;
        }
        html.dark .border-emerald-100,
        html.dark .border-emerald-200 {
            border-color: rgba(16, 185, 129, 0.35) !important;
        }
        html.dark .bg-amber-50,
        html.dark [class*="bg-amber-50/"] {
            background-color: rgba(120, 53, 15, 0.35) !important;
        }
        html.dark .bg-amber-100 {
            background-color: rgba(146, 64, 14, 0.45) !important;
        }
        html.dark .text-amber-900,
        html.dark .text-amber-800,
        html.dark .text-amber-700 {
            color: #fcd34d !important;
        }
        html.dark .border-amber-100,
        html.dark .border-amber-200 {
            border-color: rgba(245, 158, 11, 0.35) !important;
        }
        html.dark .bg-red-50,
        html.dark [class*="bg-red-50/"] {
            background-color: rgba(127, 29, 29, 0.35) !important;
        }
        html.dark .bg-red-100 {
            background-color: rgba(153, 27, 27, 0.45) !important;
        }
        html.dark .text-red-900,
        html.dark .text-red-800,
        html.dark .text-red-700 {
            color: #fca5a5 !important;
        }
        html.dark .border-red-100,
        html.dark .border-red-200 {
            border-color: rgba(239, 68, 68, 0.35) !important;
        }
        html.dark .bg-purple-50,
        html.dark .bg-purple-100 {
            background-color: rgba(88, 28, 135, 0.4) !important;
        }
        html.dark .text-purple-800 {
            color: #d8b4fe !important;
        }
        html.dark .bg-indigo-50,
        html.dark .bg-indigo-100 {
            background-color: rgba(49, 46, 129, 0.4) !important;
        }
        html.dark .text-indigo-800 {
            color: #a5b4fc !important;
        }
        html.dark .border-slate-200,
        html.dark .border-slate-100,
        html.dark .border-slate-300 {
            border-color: #334155 !important;
        }
        html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #334155 !important;
        }
        html.dark input:not([type="checkbox"]):not([type="radio"]),
        html.dark select,
        html.dark textarea {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        /* Skeleton Shimmer Animation (Light & Dark Mode) */
        @keyframes npShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .np-skeleton-shimmer {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: npShimmer 1.4s ease-in-out infinite;
        }
        html.dark .np-skeleton-shimmer {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
        }

        /* Button Loading State */
        .np-btn-loading {
            pointer-events: none;
            opacity: 0.88;
            cursor: wait;
        }

        /* ==============================================================
           Scroll-Triggered Animations (Fade-In / Slide-Up / Scale)
           ============================================================== */
        html.js-scroll-anim .np-reveal {
            opacity: 0;
            transform: translate3d(0, 22px, 0);
            transition:
                opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                border-color 0.28s ease;
            transition-delay: var(--np-stagger-delay, 0ms);
            will-change: opacity, transform;
        }
        html.js-scroll-anim .np-reveal[data-animate="fade-in"] {
            transform: translate3d(0, 0, 0);
        }
        html.js-scroll-anim .np-reveal[data-animate="scale-up"] {
            transform: translate3d(0, 14px, 0) scale(0.975);
        }
        html.js-scroll-anim .np-reveal.np-revealed {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        /* ==============================================================
           Hover Effects & Micro-Interactions
           ============================================================== */
        .np-card-hover {
            transition:
                transform 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                border-color 0.25s ease;
        }
        .np-card-hover:hover {
            transform: translate3d(0, -5px, 0) !important;
            box-shadow: 0 18px 34px -12px rgba(15, 23, 42, 0.14), 0 6px 14px -6px rgba(30, 64, 175, 0.08);
            border-color: rgba(59, 130, 246, 0.45) !important;
        }
        html.dark .np-card-hover:hover {
            box-shadow: 0 18px 36px -12px rgba(2, 6, 23, 0.65), 0 0 0 1px rgba(59, 130, 246, 0.35);
            border-color: rgba(96, 165, 250, 0.45) !important;
        }

        /* Stat Card Micro-Interaction */
        .np-stat-card {
            transition:
                transform 0.24s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.24s cubic-bezier(0.22, 1, 0.36, 1),
                border-color 0.24s ease;
        }
        .np-stat-card:hover {
            transform: translate3d(0, -3px, 0) !important;
            box-shadow: 0 12px 24px -10px rgba(30, 64, 175, 0.14);
            border-color: rgba(59, 130, 246, 0.4) !important;
        }

        /* Tactile Button & Pill Micro-Interaction */
        .np-btn-interactive,
        main button[type="submit"],
        main a.bg-blue-800,
        main a.bg-blue-700,
        main a.bg-red-600 {
            transition:
                transform 0.18s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.2s ease,
                background-color 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease;
        }
        .np-btn-interactive:hover:not(:disabled),
        main button[type="submit"]:hover:not(:disabled),
        main a.bg-blue-800:hover,
        main a.bg-blue-700:hover {
            transform: translateY(-1px);
        }
        .np-btn-interactive:active:not(:disabled),
        main button[type="submit"]:active:not(:disabled),
        main a.bg-blue-800:active,
        main a.bg-blue-700:active {
            transform: translateY(0) scale(0.98);
        }

        /* Heart / Bookmark Button Pop Micro-Interaction */
        .np-heart-btn {
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        .np-heart-btn:hover {
            transform: scale(1.1);
        }
        .np-heart-btn:active {
            transform: scale(0.9);
        }

        /* Top Nav Link Animated Indicator */
        .np-nav-link {
            position: relative;
            padding-bottom: 2px;
        }
        .np-nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -4px;
            height: 2px;
            border-radius: 9999px;
            background-color: #1e40af;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.22s cubic-bezier(0.22, 1, 0.36, 1);
        }
        html.dark .np-nav-link::after {
            background-color: #60a5fa;
        }
        .np-nav-link:hover::after,
        .np-nav-link-active::after {
            transform: scaleX(1);
        }

        /* Respect Reduced Motion Preferences */
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
            html.js-scroll-anim .np-reveal {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            .np-card-hover:hover,
            .np-stat-card:hover,
            .np-btn-interactive:hover {
                transform: none !important;
            }
        }
    </style>
</head>
@php
    $showDashboardSidebar = auth()->check() && (
        View::hasSection('sidebar') ||
        request()->routeIs(
            'dashboard',
            'admin.*',
            'properties.my',
            'properties.create',
            'properties.edit',
            'saved-properties.*',
            'enquiries.*'
        )
    );
    $sidebarMode = trim($__env->yieldContent('sidebar')) ?: (auth()->check() && auth()->user()->isAdmin() ? 'admin' : 'user');
@endphp
<body
    x-data="{
        darkMode: document.documentElement.classList.contains('dark'),
        mobileOpen: false,
        mobileSidebarOpen: false,
        sidebarCollapsed: false,
        showBackToTop: false,
        init() {
            try {
                const savedTheme = localStorage.getItem('naijaprops_theme') || localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    this.darkMode = true;
                    document.documentElement.classList.add('dark');
                } else if (savedTheme === 'light') {
                    this.darkMode = false;
                    document.documentElement.classList.remove('dark');
                }
                this.sidebarCollapsed = localStorage.getItem('naijaprops_sidebar_collapsed') === 'true';
            } catch (e) {}
            this.showBackToTop = window.scrollY > 320;
        },
        toggleTheme() {
            this.darkMode = !this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            try {
                const value = this.darkMode ? 'dark' : 'light';
                localStorage.setItem('naijaprops_theme', value);
                localStorage.setItem('theme', value);
            } catch (e) {}
        },
        toggleSidebarCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            try {
                localStorage.setItem('naijaprops_sidebar_collapsed', this.sidebarCollapsed ? 'true' : 'false');
            } catch (e) {}
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }"
    @scroll.window.passive="showBackToTop = window.scrollY > 320"
    @keydown.escape.window="mobileSidebarOpen = false; mobileOpen = false"
    class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased min-h-screen flex flex-col transition-colors duration-200"
>
    <!-- Top Loading Progress Bar -->
    <div id="np-top-progress-container" class="fixed top-0 left-0 right-0 z-50 h-1 pointer-events-none opacity-0 transition-opacity duration-200" aria-hidden="true">
        <div id="np-top-progress-bar" class="h-full w-0 bg-gradient-to-r from-blue-600 via-sky-400 to-emerald-400 shadow-[0_0_10px_rgba(59,130,246,0.75)] transition-all duration-200 ease-out"></div>
    </div>

    <!-- Full-Page / Overlay Loading Progress Modal for Important Actions -->
    <div
        id="np-loading-overlay"
        role="status"
        aria-live="polite"
        aria-busy="true"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 transition-opacity duration-200"
    >
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center space-y-4">
            <!-- Dual Ring Branded Spinner -->
            <div class="relative w-14 h-14 mx-auto flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-blue-100 dark:border-slate-800"></div>
                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-800 dark:border-t-blue-400 border-r-blue-600 dark:border-r-blue-500 animate-spin"></div>
                <span class="text-xs font-extrabold text-blue-900 dark:text-blue-300 tracking-tight">NP</span>
            </div>

            <div class="space-y-1">
                <h3 id="np-overlay-title" class="text-base font-extrabold text-slate-900 dark:text-white">
                    Processing your request...
                </h3>
                <p id="np-overlay-subtitle" class="text-xs text-slate-500 dark:text-slate-400">
                    Please wait a moment while NaijaProps completes this action.
                </p>
            </div>

            <!-- Animated Determinate/Indeterminate Progress Track -->
            <div class="space-y-1.5 pt-1">
                <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div
                        id="np-overlay-progress-bar"
                        class="h-full bg-gradient-to-r from-blue-800 via-blue-600 to-emerald-500 rounded-full transition-all duration-300 ease-out"
                        style="width: 15%"
                    ></div>
                </div>
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                    <span>Secure connection</span>
                    <span id="np-overlay-percent">15%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40 shadow-xs transition-colors duration-200">
        <div class="{{ $showDashboardSidebar ? 'max-w-full px-4 sm:px-6 lg:px-8' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }}">
            <div class="flex justify-between items-center h-16">
                <!-- Left: Sidebar Toggle (on Dashboard/Admin pages) + Brand Logo + Nav Links -->
                <div class="flex items-center space-x-3 sm:space-x-6">
                    @if($showDashboardSidebar)
                        <!-- Mobile Sidebar Drawer Trigger -->
                        <button
                            type="button"
                            @click="mobileSidebarOpen = !mobileSidebarOpen"
                            class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition"
                            aria-label="Toggle dashboard sidebar"
                            title="Open Dashboard Menu"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Desktop Sidebar Collapse/Expand Trigger -->
                        <button
                            type="button"
                            @click="toggleSidebarCollapse()"
                            class="hidden lg:inline-flex items-center justify-center p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-blue-800 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition"
                            :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                            aria-label="Toggle sidebar width"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/>
                            </svg>
                        </button>
                    @endif

                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-blue-800 flex items-center justify-center text-white font-bold text-lg shadow-xs group-hover:bg-blue-700 group-hover:scale-105 transition duration-200">
                            NP
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-blue-900 dark:text-blue-400">Naija<span class="text-blue-600 dark:text-blue-300">Props</span></span>
                            <span class="hidden sm:inline-block text-[10px] font-semibold uppercase tracking-wider bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded ml-1 border border-blue-200 dark:border-blue-700">NG</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600 dark:text-slate-300">
                        <a href="{{ route('home') }}" class="np-nav-link {{ request()->routeIs('home') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                            Home
                        </a>
                        <a href="{{ route('properties.index') }}" class="np-nav-link {{ request()->routeIs('properties.index', 'properties.show') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                            Browse Properties
                        </a>
                        <a href="{{ route('recommendations.index') }}" class="np-nav-link {{ request()->routeIs('recommendations.*') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }} inline-flex items-center gap-1">
                            <span>AI Recommendations</span>
                        </a>
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="np-nav-link {{ request()->routeIs('admin.dashboard') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    Admin Dashboard
                                </a>
                                <a href="{{ route('admin.properties.index') }}" class="np-nav-link {{ request()->routeIs('admin.properties.*') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    Manage Properties
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="np-nav-link {{ request()->routeIs('admin.users.*') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    Manage Users
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="np-nav-link {{ request()->routeIs('admin.reports.*') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    Reports
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="np-nav-link {{ request()->routeIs('dashboard') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    My Dashboard
                                </a>
                                <a href="{{ route('saved-properties.index') }}" class="np-nav-link {{ request()->routeIs('saved-properties.*') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    Saved Properties
                                </a>
                                <a href="{{ route('enquiries.index') }}" class="np-nav-link {{ request()->routeIs('enquiries.*') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                    Enquiries
                                </a>
                                @can('create', \App\Models\Property::class)
                                    <a href="{{ route('properties.my') }}" class="np-nav-link {{ request()->routeIs('properties.my') ? 'np-nav-link-active text-blue-800 dark:text-blue-400 font-semibold' : 'hover:text-blue-800 dark:hover:text-blue-400 transition' }}">
                                        My Listings
                                    </a>
                                    <a href="{{ route('properties.create') }}" class="np-btn-interactive inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-900/40 hover:bg-blue-100 dark:hover:bg-blue-900/70 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700 font-semibold text-xs px-3 py-1.5 rounded-lg transition">
                                        + List Property
                                    </a>
                                @endcan
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Side: Theme Toggle + Auth Actions -->
                <div class="hidden md:flex items-center space-x-3">
                    <!-- Light / Dark Theme Toggle Button -->
                    <button
                        type="button"
                        @click="toggleTheme()"
                        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                        aria-label="Toggle Light/Dark Theme"
                        class="np-btn-interactive inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                    >
                        <!-- Sun Icon (Dark Mode Active) -->
                        <svg x-show="darkMode" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <!-- Moon Icon (Light Mode Active) -->
                        <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <span x-text="darkMode ? 'Light' : 'Dark'"></span>
                    </button>

                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-800 dark:hover:text-blue-400 px-3 py-2 rounded-lg transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="np-btn-interactive text-sm font-semibold bg-blue-800 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow-xs transition">
                            Create Account
                        </a>
                    @else
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 leading-none">{{ auth()->user()->full_name }}</p>
                                <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full mt-1
                                    @if(auth()->user()->isAdmin()) bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300
                                    @elseif(auth()->user()->canListProperties()) bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                    @else bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 @endif">
                                    {{ auth()->user()->role_label }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" data-loading-overlay="true" data-loading-title="Signing Out..." data-loading-subtitle="Closing your NaijaProps session securely...">
                                @csrf
                                <button type="submit" data-loading-text="Signing out..." class="np-btn-interactive inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 hover:border-red-200 bg-slate-50 dark:bg-slate-800 hover:bg-red-50 px-3 py-2 rounded-lg transition">
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>

                <!-- Mobile Right Controls: Theme Toggle + Mobile Menu Button -->
                <div class="flex items-center space-x-2 md:hidden">
                    <button
                        type="button"
                        @click="toggleTheme()"
                        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                        aria-label="Toggle theme"
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 transition"
                    >
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg x-show="!darkMode" class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <button @click="mobileOpen = !mobileOpen" type="button" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 p-2 rounded-lg focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div
            x-show="mobileOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-3 pb-4 space-y-2 shadow-lg"
        >
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Home</a>
            <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Browse Properties</a>
            <a href="{{ route('recommendations.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">AI Recommendations</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Admin Dashboard</a>
                    <a href="{{ route('admin.properties.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Manage Properties</a>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Manage Users</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Manage Reports</a>
                    <a href="{{ route('enquiries.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Enquiries</a>
                @else
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">My Dashboard</a>
                    <a href="{{ route('saved-properties.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Saved Properties</a>
                    <a href="{{ route('enquiries.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Enquiries</a>
                    @can('create', \App\Models\Property::class)
                        <a href="{{ route('properties.my') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">My Listings</a>
                        <a href="{{ route('properties.create') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-800 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/40">+ List New Property</a>
                    @endcan
                @endif
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between px-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()->full_name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->role_label }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" data-loading-overlay="true" data-loading-title="Signing Out..." data-loading-subtitle="Closing your NaijaProps session securely...">
                        @csrf
                        <button type="submit" data-loading-text="Signing out..." class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 bg-red-50 dark:bg-red-900/40 dark:text-red-300 px-3 py-1.5 rounded-lg">Logout</button>
                    </form>
                </div>
            @else
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col space-y-2">
                    <a href="{{ route('login') }}" class="w-full text-center py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-200">Sign In</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold">Create Account</a>
                </div>
            @endguest
        </div>
    </header>

    @if($showDashboardSidebar)
        <!-- Dashboard Layout with Responsive & Collapsible Sidebar -->
        <div class="flex-1 flex min-h-[calc(100vh-4rem)]">
            <x-dashboard-sidebar :mode="$sidebarMode" />

            <div class="flex-1 min-w-0 flex flex-col">
                <!-- Mobile Quick Sidebar Bar for Dashboard Pages -->
                <div class="lg:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-2.5 flex items-center justify-between">
                    <button
                        type="button"
                        @click="mobileSidebarOpen = true"
                        class="inline-flex items-center gap-2 text-xs font-bold text-blue-800 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 px-3 py-1.5 rounded-xl"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/>
                        </svg>
                        <span>{{ $sidebarMode === 'admin' ? 'Admin Menu' : 'Dashboard Menu' }}</span>
                    </button>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>

                <!-- Flash Notifications inside Dashboard Main Content -->
                @if(session('success'))
                    <div
                        x-data="{ showAlert: true }"
                        x-show="showAlert"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full"
                    >
                        <div class="bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-4 py-3 rounded-xl flex items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-center space-x-2.5 text-sm font-medium">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button
                                type="button"
                                @click="showAlert = false"
                                class="text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 p-1 rounded-lg transition shrink-0"
                                aria-label="Dismiss notification"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div
                        x-data="{ showErrorAlert: true }"
                        x-show="showErrorAlert"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full"
                    >
                        <div class="bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl shadow-xs">
                            <div class="flex items-center justify-between gap-3 mb-1">
                                <div class="flex items-center space-x-2 text-sm font-semibold">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Please check the following errors:</span>
                                </div>
                                <button
                                    type="button"
                                    @click="showErrorAlert = false"
                                    class="text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 p-1 rounded-lg transition shrink-0"
                                    aria-label="Dismiss error banner"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <ul class="list-disc list-inside text-xs space-y-0.5 text-red-700 dark:text-red-300">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <main class="flex-1">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <!-- Standard Public / Auth Page Layout -->
        @if(session('success'))
            <div
                x-data="{ showAlert: true }"
                x-show="showAlert"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full"
            >
                <div class="bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-4 py-3 rounded-xl flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center space-x-2.5 text-sm font-medium">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button
                        type="button"
                        @click="showAlert = false"
                        class="text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 p-1 rounded-lg transition shrink-0"
                        aria-label="Dismiss notification"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div
                x-data="{ showErrorAlert: true }"
                x-show="showErrorAlert"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full"
            >
                <div class="bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl shadow-xs">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <div class="flex items-center space-x-2 text-sm font-semibold">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Please check the following errors:</span>
                        </div>
                        <button
                            type="button"
                            @click="showErrorAlert = false"
                            class="text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 p-1 rounded-lg transition shrink-0"
                            aria-label="Dismiss error banner"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-red-700 dark:text-red-300">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <main class="flex-1">
            @yield('content')
        </main>
    @endif

    <!-- Smooth-Scroll Floating Back-to-Top Button -->
    <button
        type="button"
        x-show="showBackToTop"
        x-cloak
        @click="scrollToTop()"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-4 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-90"
        title="Back to top"
        aria-label="Scroll back to top"
        class="fixed bottom-6 right-6 z-40 w-11 h-11 rounded-2xl bg-blue-800 hover:bg-blue-700 text-white shadow-lg shadow-blue-950/30 border border-blue-600/40 flex items-center justify-center np-btn-interactive focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-slate-800/80">
                <!-- Col 1: Brand & Overview -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-700 flex items-center justify-center text-white font-extrabold text-sm shadow-xs">NP</div>
                        <span class="text-white font-extrabold text-lg tracking-tight">NaijaProps</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-950 text-blue-300 border border-blue-800 px-2 py-0.5 rounded">NG</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                        Nigeria's centralised web-based property listing and trading platform. Discover verified houses, duplexes, apartments, and land with transparent Naira (₦) pricing and content-based AI recommendations.
                    </p>
                </div>

                <!-- Col 2: Nigerian Markets -->
                <div class="space-y-2.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Nigerian Markets</h4>
                    <ul class="space-y-1.5 text-xs">
                        <li>
                            <a href="{{ route('properties.index', ['location' => 'Lagos']) }}" class="hover:text-white transition">Lagos Properties (Lekki, VI, Ikoyi)</a>
                        </li>
                        <li>
                            <a href="{{ route('properties.index', ['location' => 'Abuja']) }}" class="hover:text-white transition">Abuja Properties (Maitama, Guzape, Wuse)</a>
                        </li>
                        <li>
                            <a href="{{ route('properties.index', ['location' => 'Port Harcourt']) }}" class="hover:text-white transition">Port Harcourt Properties (GRA, Peter Odili)</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Quick Platform Links -->
                <div class="space-y-2.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Quick Navigation</h4>
                    <ul class="space-y-1.5 text-xs">
                        <li><a href="{{ route('properties.index') }}" class="hover:text-white transition">Browse All Listings</a></li>
                        <li><a href="{{ route('recommendations.index') }}" class="hover:text-white transition">AI Recommendation Engine</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">My Account Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition">Sign In / Register</a></li>
                        @endguest
                    </ul>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>
                    &copy; {{ date('Y') }} NaijaProps — Web-Based Property Listing &amp; Trading System.
                </p>
                <p>
                    Serving Lagos, Abuja &amp; Port Harcourt &bull; All prices displayed in Nigerian Naira (₦).
                </p>
            </div>
        </div>
    </footer>

    <!-- Global Loading Experience Controller: Button Spinners, Skeleton Loaders & Progress Overlay -->
    <script>
        (function () {
            const topProgressContainer = document.getElementById('np-top-progress-container');
            const topProgressBar = document.getElementById('np-top-progress-bar');
            const overlay = document.getElementById('np-loading-overlay');
            const overlayTitle = document.getElementById('np-overlay-title');
            const overlaySubtitle = document.getElementById('np-overlay-subtitle');
            const overlayProgressBar = document.getElementById('np-overlay-progress-bar');
            const overlayPercent = document.getElementById('np-overlay-percent');

            let topTimer = null;
            let overlayTimer = null;

            const SPINNER_SVG = '<svg class="animate-spin h-4 w-4 shrink-0 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            function startTopProgress() {
                if (!topProgressContainer || !topProgressBar) return;
                clearInterval(topTimer);
                topProgressContainer.classList.remove('opacity-0');
                let pct = 18;
                topProgressBar.style.width = pct + '%';
                topTimer = setInterval(function () {
                    if (pct < 90) {
                        pct += Math.max(2, Math.round((90 - pct) * 0.18));
                        topProgressBar.style.width = pct + '%';
                    }
                }, 140);
            }

            function finishTopProgress() {
                if (!topProgressContainer || !topProgressBar) return;
                clearInterval(topTimer);
                topProgressBar.style.width = '100%';
                setTimeout(function () {
                    topProgressContainer.classList.add('opacity-0');
                    setTimeout(function () {
                        topProgressBar.style.width = '0%';
                    }, 200);
                }, 180);
            }

            function showOverlay(title, subtitle) {
                if (!overlay) return;
                if (overlayTitle && title) overlayTitle.textContent = title;
                if (overlaySubtitle && subtitle) overlaySubtitle.textContent = subtitle;
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');

                clearInterval(overlayTimer);
                let pct = 18;
                if (overlayProgressBar) overlayProgressBar.style.width = pct + '%';
                if (overlayPercent) overlayPercent.textContent = pct + '%';

                overlayTimer = setInterval(function () {
                    if (pct < 92) {
                        pct += Math.max(3, Math.round((92 - pct) * 0.2));
                        if (overlayProgressBar) overlayProgressBar.style.width = pct + '%';
                        if (overlayPercent) overlayPercent.textContent = pct + '%';
                    }
                }, 160);
            }

            function hideOverlay() {
                if (!overlay) return;
                clearInterval(overlayTimer);
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }

            function showSkeletons() {
                document.querySelectorAll('[data-np-skeleton]').forEach(function (skeletonEl) {
                    skeletonEl.classList.remove('hidden');
                });
                document.querySelectorAll('[data-np-content]').forEach(function (contentEl) {
                    contentEl.classList.add('opacity-40', 'pointer-events-none');
                });
            }

            function hideSkeletons() {
                document.querySelectorAll('[data-np-skeleton]').forEach(function (skeletonEl) {
                    skeletonEl.classList.add('hidden');
                });
                document.querySelectorAll('[data-np-content]').forEach(function (contentEl) {
                    contentEl.classList.remove('opacity-40', 'pointer-events-none');
                });
            }

            function inferOverlayMessaging(form, submitter) {
                const explicitTitle = (submitter && submitter.getAttribute('data-loading-title')) || form.getAttribute('data-loading-title');
                const explicitSubtitle = (submitter && submitter.getAttribute('data-loading-subtitle')) || form.getAttribute('data-loading-subtitle');
                if (explicitTitle) {
                    return {
                        title: explicitTitle,
                        subtitle: explicitSubtitle || 'Please wait while NaijaProps processes your request...'
                    };
                }

                const action = (form.getAttribute('action') || '').toLowerCase();
                const methodInput = form.querySelector('input[name="_method"]');
                const method = (methodInput ? methodInput.value : (form.getAttribute('method') || 'GET')).toUpperCase();

                if (action.includes('/login')) {
                    return { title: 'Signing in to NaijaProps...', subtitle: 'Verifying your credentials and loading your dashboard...' };
                }
                if (action.includes('/register')) {
                    return { title: 'Creating your NaijaProps Account...', subtitle: 'Setting up your profile and personalised preferences...' };
                }
                if (action.includes('/logout')) {
                    return { title: 'Signing Out...', subtitle: 'Closing your NaijaProps session securely...' };
                }
                if (action.includes('/enquiries')) {
                    return { title: method === 'POST' ? 'Sending Property Enquiry...' : 'Updating Enquiry...', subtitle: 'Delivering your message to the property provider...' };
                }
                if (action.includes('/reports')) {
                    return { title: 'Processing Listing Report...', subtitle: 'Updating NaijaProps Trust & Safety moderation queue...' };
                }
                if (action.includes('/saved-properties') || action.includes('/save')) {
                    return { title: 'Updating Saved Properties...', subtitle: 'Syncing your property watchlist...' };
                }
                if (action.includes('/admin/')) {
                    return { title: 'Applying Administrator Action...', subtitle: 'Updating platform records and permissions...' };
                }
                if (action.includes('/properties')) {
                    if (method === 'DELETE') {
                        return { title: 'Deleting Property Listing...', subtitle: 'Removing listing and associated gallery images...' };
                    }
                    if (method === 'PUT' || method === 'PATCH') {
                        return { title: 'Saving Property Updates...', subtitle: 'Updating listing details and media gallery...' };
                    }
                    if (method === 'POST') {
                        return { title: 'Publishing Property Listing...', subtitle: 'Uploading photos and indexing your listing on NaijaProps...' };
                    }
                }
                if (action.includes('/recommendations')) {
                    return { title: 'Ranking Properties with AI Engine...', subtitle: 'Scoring location, property type, Naira price range, and bedrooms...' };
                }

                return {
                    title: 'Loading Results...',
                    subtitle: 'Fetching verified Nigerian property data...'
                };
            }

            function inferButtonLoadingText(btn, form) {
                const explicit = btn.getAttribute('data-loading-text');
                if (explicit) return explicit;

                const rawText = (btn.textContent || btn.value || '').trim();
                if (!rawText) return '';

                const lower = rawText.toLowerCase();
                if (lower.includes('sign in') || lower.includes('login')) return 'Signing in...';
                if (lower.includes('create') && lower.includes('account')) return 'Creating account...';
                if (lower.includes('register')) return 'Registering...';
                if (lower.includes('publish')) return 'Publishing listing...';
                if (lower.includes('save')) return 'Saving...';
                if (lower.includes('send')) return 'Sending...';
                if (lower.includes('submit')) return 'Submitting...';
                if (lower.includes('apply') && lower.includes('filter')) return 'Filtering...';
                if (lower.includes('search')) return 'Searching...';
                if (lower.includes('filter')) return 'Filtering...';
                if (lower.includes('rank')) return 'Ranking...';
                if (lower.includes('approve')) return 'Approving...';
                if (lower.includes('reject') || lower.includes('hold')) return 'Updating...';
                if (lower.includes('delete') || lower.includes('remove')) return 'Removing...';
                if (lower.includes('unsave')) return 'Removing...';
                if (lower.includes('deactivate')) return 'Deactivating...';
                if (lower.includes('activate')) return 'Activating...';
                if (lower.includes('resolve')) return 'Resolving...';
                if (lower.includes('mark')) return 'Updating...';
                if (lower.includes('logout')) return 'Signing out...';
                return 'Processing...';
            }

            function activateButtonSpinner(btn, form) {
                if (!btn || btn.dataset.npSpinning === 'true') return;
                btn.dataset.npSpinning = 'true';
                btn.classList.add('np-btn-loading');

                if (btn.tagName === 'BUTTON') {
                    const isIconOnly = btn.textContent.trim() === '' || btn.hasAttribute('data-icon-only');
                    if (isIconOnly) {
                        btn.innerHTML = SPINNER_SVG;
                    } else {
                        const loadingText = inferButtonLoadingText(btn, form);
                        btn.innerHTML = '<span class="inline-flex items-center justify-center gap-2">' + SPINNER_SVG + '<span>' + loadingText + '</span></span>';
                    }
                } else if (btn.tagName === 'INPUT') {
                    btn.value = inferButtonLoadingText(btn, form);
                }

                // Disable after current event tick so native form submission includes button value if any
                setTimeout(function () {
                    btn.disabled = true;
                }, 10);
            }

            // Global Form Submit Handler
            document.addEventListener('submit', function (event) {
                if (event.defaultPrevented) return;
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                const submitter = event.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitter) {
                    activateButtonSpinner(submitter, form);
                }

                startTopProgress();

                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                const isGetFilter = method === 'GET' && form.getAttribute('data-loading-overlay') !== 'true';

                if (isGetFilter) {
                    // For GET search/filter forms, show skeletons in property grids/tables/lists
                    showSkeletons();
                } else {
                    // For POST/PUT/PATCH/DELETE or explicit overlay forms, show full-page progress overlay
                    const msg = inferOverlayMessaging(form, submitter);
                    showOverlay(msg.title, msg.subtitle);
                }
            });

            // Handle auto-submitting selects (e.g. onchange="this.form.submit()")
            document.addEventListener('change', function (event) {
                const target = event.target;
                if (target instanceof HTMLSelectElement && target.form) {
                    const onchangeAttr = target.getAttribute('onchange') || '';
                    if (onchangeAttr.includes('submit')) {
                        startTopProgress();
                        const msg = inferOverlayMessaging(target.form, null);
                        showOverlay(msg.title, msg.subtitle);
                    }
                }
            });

            // Handle filter pills, reset buttons, tabs, and pagination links to show skeletons
            document.addEventListener('click', function (event) {
                const link = event.target.closest('a[href]');
                if (!link) return;
                if (link.target === '_blank' || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

                const href = link.getAttribute('href') || '';
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

                startTopProgress();

                if (
                    link.hasAttribute('data-skeleton-trigger') ||
                    href.includes('page=') ||
                    href.includes('tab=') ||
                    href.includes('transaction_type=') ||
                    href.includes('location=') ||
                    href.includes('flagged=') ||
                    link.closest('[data-skeleton-nav]')
                ) {
                    showSkeletons();
                }

                if (link.hasAttribute('data-loading-overlay')) {
                    showOverlay(
                        link.getAttribute('data-loading-title') || 'Loading Page...',
                        link.getAttribute('data-loading-subtitle') || 'Please wait a moment...'
                    );
                }
            });

            // Progressive Image Skeleton Shimmer for Property Card & Table Images
            function initImageSkeletons() {
                document.querySelectorAll('main img').forEach(function (img) {
                    if (img.complete && img.naturalHeight > 0) return;
                    img.classList.add('np-skeleton-shimmer');
                    const clearShimmer = function () {
                        img.classList.remove('np-skeleton-shimmer');
                    };
                    img.addEventListener('load', clearShimmer, { once: true });
                    img.addEventListener('error', clearShimmer, { once: true });
                });
            }

            // Reset states when navigating back/forward via bfcache
            window.addEventListener('pageshow', function () {
                hideOverlay();
                hideSkeletons();
                finishTopProgress();
                document.querySelectorAll('[data-np-spinning="true"]').forEach(function (btn) {
                    btn.disabled = false;
                    btn.dataset.npSpinning = 'false';
                    btn.classList.remove('np-btn-loading');
                });
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initImageSkeletons);
            } else {
                initImageSkeletons();
            }

            window.NaijaPropsLoader = {
                startTopProgress: startTopProgress,
                finishTopProgress: finishTopProgress,
                showOverlay: showOverlay,
                hideOverlay: hideOverlay,
                showSkeletons: showSkeletons,
                hideSkeletons: hideSkeletons
            };
        })();

        // =========================================================================
        // Smooth Anchor Scrolling & Scroll-Triggered Animations (Fade-In / Slide-Up)
        // =========================================================================
        (function () {
            // 1. Smooth Scroll for In-Page Anchor Links (#enquiry-section, #report-section, etc.)
            document.addEventListener('click', function (event) {
                const anchor = event.target.closest('a[href^="#"]');
                if (!anchor) return;
                const hash = anchor.getAttribute('href');
                if (!hash || hash === '#') return;
                const targetEl = document.querySelector(hash);
                if (targetEl) {
                    event.preventDefault();
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    if (history.pushState) {
                        history.pushState(null, '', hash);
                    }
                }
            });

            // 2. Scroll-Triggered Reveal Observer & Micro-Interaction Decorator
            function initScrollPolish() {
                const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                // Automatically tag property cards, recommendation cards, stat cards, and sections
                // if they don't already have explicit data-animate attributes
                const autoRevealGroups = document.querySelectorAll(
                    'main [data-np-content].grid, main .grid.gap-6, main .grid.gap-5, main .grid.gap-4, main [data-np-content].space-y-4'
                );

                autoRevealGroups.forEach(function (group) {
                    Array.from(group.children).forEach(function (child, idx) {
                        if (child.hasAttribute('data-np-skeleton')) return;
                        if (!child.hasAttribute('data-animate')) {
                            child.setAttribute('data-animate', 'fade-up');
                        }
                        if (!child.style.getPropertyValue('--np-stagger-delay')) {
                            const delayMs = Math.min(idx % 6, 5) * 65;
                            child.style.setProperty('--np-stagger-delay', delayMs + 'ms');
                        }
                    });
                });

                // Add hover elevation class to property cards & interactive cards
                document.querySelectorAll('main .group.rounded-2xl').forEach(function (card) {
                    card.classList.add('np-card-hover');
                });

                // Add subtle hover lift to stat cards across dashboards
                document.querySelectorAll('main .grid.gap-4 > .rounded-2xl, main .grid.gap-5 > .rounded-2xl').forEach(function (stat) {
                    if (!stat.classList.contains('np-card-hover')) {
                        stat.classList.add('np-stat-card');
                    }
                });

                // Add heart micro-interaction to save/bookmark buttons
                document.querySelectorAll('form[action*="saved-properties"] button').forEach(function (btn) {
                    btn.classList.add('np-heart-btn');
                });

                const animElements = document.querySelectorAll('[data-animate]');
                if (!animElements.length) return;

                if (prefersReducedMotion || !('IntersectionObserver' in window)) {
                    animElements.forEach(function (el) {
                        el.classList.add('np-revealed');
                    });
                    return;
                }

                const observer = new IntersectionObserver(
                    function (entries, obs) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('np-revealed');
                                obs.unobserve(entry.target);
                            }
                        });
                    },
                    {
                        root: null,
                        rootMargin: '0px 0px -40px 0px',
                        threshold: 0.08
                    }
                );

                animElements.forEach(function (el) {
                    const explicitDelay = el.getAttribute('data-animate-delay');
                    if (explicitDelay) {
                        el.style.setProperty('--np-stagger-delay', explicitDelay + 'ms');
                    }
                    el.classList.add('np-reveal');

                    // Reveal immediately if already well inside initial viewport
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight * 0.92 && rect.bottom > 0) {
                        requestAnimationFrame(function () {
                            el.classList.add('np-revealed');
                        });
                    } else {
                        observer.observe(el);
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initScrollPolish);
            } else {
                initScrollPolish();
            }
        })();
    </script>
</body>
</html>
