<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'NaijaProps') . ' – Nigerian Property Listing & Trading System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-blue-800 flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:bg-blue-700 transition">
                            NP
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-blue-900">Naija<span class="text-blue-600">Props</span></span>
                            <span class="hidden sm:inline-block text-[10px] font-semibold uppercase tracking-wider bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded ml-1 border border-blue-200">NG</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                            Home
                        </a>
                        <a href="{{ route('properties.index') }}" class="{{ request()->routeIs('properties.index', 'properties.show') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                            Browse Properties
                        </a>
                        <a href="{{ route('recommendations.index') }}" class="{{ request()->routeIs('recommendations.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }} inline-flex items-center gap-1">
                            <span>AI Recommendations</span>
                        </a>
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Admin Dashboard
                                </a>
                                <a href="{{ route('admin.properties.index') }}" class="{{ request()->routeIs('admin.properties.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Manage Properties
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Manage Users
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Reports
                                </a>
                                <a href="{{ route('enquiries.index') }}" class="{{ request()->routeIs('enquiries.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Enquiries
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    My Dashboard
                                </a>
                                <a href="{{ route('saved-properties.index') }}" class="{{ request()->routeIs('saved-properties.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Saved Properties
                                </a>
                                <a href="{{ route('enquiries.index') }}" class="{{ request()->routeIs('enquiries.*') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                    Enquiries
                                </a>
                                @can('create', \App\Models\Property::class)
                                    <a href="{{ route('properties.my') }}" class="{{ request()->routeIs('properties.my') ? 'text-blue-800 font-semibold' : 'hover:text-blue-800 transition' }}">
                                        My Listings
                                    </a>
                                    <a href="{{ route('properties.create') }}" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 font-semibold text-xs px-3 py-1.5 rounded-lg transition">
                                        + List Property
                                    </a>
                                @endcan
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Side Auth Actions -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-800 px-3 py-2 rounded-lg transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-blue-800 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow-sm transition">
                            Create Account
                        </a>
                    @else
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800 leading-none">{{ auth()->user()->full_name }}</p>
                                <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full mt-1
                                    @if(auth()->user()->isAdmin()) bg-purple-100 text-purple-800
                                    @elseif(auth()->user()->canListProperties()) bg-blue-100 text-blue-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    {{ auth()->user()->role_label }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-slate-600 hover:text-red-600 border border-slate-200 hover:border-red-200 bg-slate-50 hover:bg-red-50 px-3 py-2 rounded-lg transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="text-slate-600 hover:text-slate-900 p-2 rounded-lg focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileOpen" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Home</a>
            <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Browse Properties</a>
            <a href="{{ route('recommendations.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">AI Recommendations</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Admin Dashboard</a>
                    <a href="{{ route('admin.properties.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Manage Properties</a>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Manage Users</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Manage Reports</a>
                    <a href="{{ route('enquiries.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Enquiries</a>
                @else
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">My Dashboard</a>
                    <a href="{{ route('saved-properties.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Saved Properties</a>
                    <a href="{{ route('enquiries.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Enquiries</a>
                    @can('create', \App\Models\Property::class)
                        <a href="{{ route('properties.my') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">My Listings</a>
                        <a href="{{ route('properties.create') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-800 bg-blue-50">+ List New Property</a>
                    @endcan
                @endif
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between px-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->full_name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->role_label }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg">Logout</button>
                    </form>
                </div>
            @else
                <div class="pt-2 border-t border-slate-100 flex flex-col space-y-2">
                    <a href="{{ route('login') }}" class="w-full text-center py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700">Sign In</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold">Create Account</a>
                </div>
            @endguest
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2 text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
                <div class="flex items-center space-x-2 text-sm font-semibold mb-1">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Please check the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded bg-blue-700 flex items-center justify-center text-white font-bold text-xs">NP</div>
                    <span class="text-white font-bold text-lg">NaijaProps</span>
                    <span class="text-xs text-slate-500">— Nigerian Property Listing &amp; Trading System</span>
                </div>
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} NaijaProps. Serving Lagos, Abuja &amp; Port Harcourt. All prices in Nigerian Naira (₦).
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
