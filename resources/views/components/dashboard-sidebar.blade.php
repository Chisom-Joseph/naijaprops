@props([
    'mode' => null,
])

@php
    $currentUser = auth()->user();
    $isAdminMode = $mode === 'admin' || ($currentUser && $currentUser->isAdmin());

    // Lightweight sidebar badge metrics for authenticated user
    $pendingReportsBadge = 0;
    $pendingEnquiriesBadge = 0;
    $savedCountBadge = 0;
    $myListingsBadge = 0;

    if ($currentUser) {
        $savedCountBadge = $currentUser->savedProperties()->count();

        if ($currentUser->isAdmin()) {
            $pendingReportsBadge = \App\Models\Report::where('status', 'pending')->count();
            $pendingEnquiriesBadge = \App\Models\Enquiry::where('status', 'pending')->count();
        } elseif ($currentUser->canListProperties()) {
            $myListingsBadge = $currentUser->properties()->count();
            $pendingEnquiriesBadge = \App\Models\Enquiry::whereHas('property', function ($q) use ($currentUser) {
                $q->where('owner_id', $currentUser->id);
            })->where('status', 'pending')->count();
        } else {
            $pendingEnquiriesBadge = $currentUser->enquiries()->count();
        }
    }

    $initials = $currentUser
        ? collect(explode(' ', trim($currentUser->full_name)))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('')
        : 'NP';
@endphp

@if($currentUser)
    <!-- Mobile Off-Canvas Sidebar Backdrop -->
    <div
        x-show="mobileSidebarOpen"
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileSidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
        aria-hidden="true"
    ></div>

    <!-- Sidebar Container (Slide-over Drawer on Mobile, Sticky Full-Height Column on Desktop) -->
    <aside
        x-cloak
        :class="[
            mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
        ]"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 ease-in-out lg:sticky lg:top-16 lg:z-30 lg:h-[calc(100vh-4rem)] lg:max-h-[calc(100vh-4rem)] lg:self-start shrink-0 shadow-xl lg:shadow-none overflow-hidden"
        aria-label="{{ $isAdminMode ? 'Admin Dashboard Sidebar' : 'User Dashboard Sidebar' }}"
    >
        <!-- Sidebar Top Profile & Collapse Header -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
            <div class="flex items-center gap-3 min-w-0" :class="sidebarCollapsed ? 'lg:justify-center lg:w-full' : ''">
                <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center font-extrabold text-sm text-white shadow-xs
                    {{ $isAdminMode ? 'bg-gradient-to-br from-purple-700 to-blue-800' : 'bg-gradient-to-br from-blue-800 to-blue-600' }}">
                    {{ $initials }}
                </div>
                <div x-show="!sidebarCollapsed" x-transition.opacity class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                        {{ $currentUser->full_name }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            @if($currentUser->isAdmin()) bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300
                            @elseif($currentUser->canListProperties()) bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                            @else bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 @endif">
                            {{ $currentUser->role_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Desktop Collapse Button (shown in header when expanded) -->
            <button
                type="button"
                @click="toggleSidebarCollapse()"
                x-show="!sidebarCollapsed"
                title="Collapse sidebar"
                class="hidden lg:inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Mobile Drawer Close Button -->
            <button
                type="button"
                @click="mobileSidebarOpen = false"
                class="lg:hidden inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                aria-label="Close sidebar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Expand Button Strip when Desktop Sidebar is Collapsed -->
        <div x-show="sidebarCollapsed" class="hidden lg:flex justify-center py-2 border-b border-slate-100 dark:border-slate-800/80">
            <button
                type="button"
                @click="toggleSidebarCollapse()"
                title="Expand sidebar"
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-500 hover:text-blue-700 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            @if($isAdminMode)
                <!-- ADMIN DASHBOARD NAVIGATION -->
                <div class="space-y-1">
                    <p
                        x-show="!sidebarCollapsed"
                        class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2"
                    >
                        Admin Control Center
                    </p>

                    <!-- Admin Overview -->
                    <a
                        href="{{ route('admin.dashboard') }}"
                        title="Admin Overview"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('admin.dashboard')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Dashboard Overview</span>
                    </a>

                    <!-- Manage Properties -->
                    <a
                        href="{{ route('admin.properties.index') }}"
                        title="Manage Properties"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('admin.properties.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.properties.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Manage Properties</span>
                        </div>
                    </a>

                    <!-- Manage Users -->
                    <a
                        href="{{ route('admin.users.index') }}"
                        title="Manage Users"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('admin.users.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Manage Users</span>
                        </div>
                    </a>

                    <!-- Suspicious Listing Reports -->
                    <a
                        href="{{ route('admin.reports.index') }}"
                        title="Suspicious Reports"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="relative flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('admin.reports.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-red-600 dark:group-hover:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Listing Reports</span>
                        </div>
                        @if($pendingReportsBadge > 0)
                            <span
                                x-show="!sidebarCollapsed"
                                class="px-2 py-0.5 text-[11px] font-extrabold rounded-full {{ request()->routeIs('admin.reports.*') ? 'bg-white text-blue-900' : 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300' }}"
                            >
                                {{ $pendingReportsBadge }}
                            </span>
                            <span
                                x-show="sidebarCollapsed"
                                class="hidden lg:block absolute top-1.5 right-3 w-2 h-2 rounded-full bg-red-500"
                            ></span>
                        @endif
                    </a>

                    <!-- Platform Enquiries -->
                    <a
                        href="{{ route('enquiries.index') }}"
                        title="Property Enquiries"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('enquiries.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('enquiries.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">All Enquiries</span>
                        </div>
                        @if($pendingEnquiriesBadge > 0)
                            <span
                                x-show="!sidebarCollapsed"
                                class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ request()->routeIs('enquiries.*') ? 'bg-white text-blue-900' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' }}"
                            >
                                {{ $pendingEnquiriesBadge }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Marketplace & Actions Section for Admin -->
                <div class="space-y-1 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <p
                        x-show="!sidebarCollapsed"
                        class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2"
                    >
                        Marketplace Shortcuts
                    </p>

                    <a
                        href="{{ route('properties.create') }}"
                        title="Post New Property"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('properties.create')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-blue-800 dark:text-blue-400 bg-blue-50/70 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/40' }}"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">List New Property</span>
                    </a>

                    <a
                        href="{{ route('properties.index') }}"
                        title="Browse Marketplace"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 transition"
                    >
                        <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Browse Marketplace</span>
                    </a>

                    <a
                        href="{{ route('recommendations.index') }}"
                        title="AI Recommendations"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 transition"
                    >
                        <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">AI Recommendations</span>
                    </a>
                </div>
            @else
                <!-- USER DASHBOARD NAVIGATION (Owners, Agents, Buyers/Tenants) -->
                <div class="space-y-1">
                    <p
                        x-show="!sidebarCollapsed"
                        class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2"
                    >
                        My Dashboard
                    </p>

                    <!-- Dashboard Overview -->
                    <a
                        href="{{ route('dashboard') }}"
                        title="Dashboard Overview"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('dashboard')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Overview</span>
                    </a>

                    @can('create', \App\Models\Property::class)
                        <!-- My Listings (Owner / Agent) -->
                        <a
                            href="{{ route('properties.my') }}"
                            title="My Property Listings"
                            :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                            class="flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                                {{ request()->routeIs('properties.my', 'properties.edit')
                                    ? 'bg-blue-800 text-white shadow-xs'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('properties.my', 'properties.edit') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span x-show="!sidebarCollapsed" class="truncate">My Listings</span>
                            </div>
                            @if($myListingsBadge > 0)
                                <span
                                    x-show="!sidebarCollapsed"
                                    class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ request()->routeIs('properties.my', 'properties.edit') ? 'bg-white text-blue-900' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}"
                                >
                                    {{ $myListingsBadge }}
                                </span>
                            @endif
                        </a>

                        <!-- Create Listing -->
                        <a
                            href="{{ route('properties.create') }}"
                            title="List New Property"
                            :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                            class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                                {{ request()->routeIs('properties.create')
                                    ? 'bg-blue-800 text-white shadow-xs'
                                    : 'text-blue-800 dark:text-blue-400 bg-blue-50/80 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/40' }}"
                        >
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">+ List Property</span>
                        </a>
                    @endcan

                    <!-- Saved Properties -->
                    <a
                        href="{{ route('saved-properties.index') }}"
                        title="Saved Properties"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('saved-properties.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('saved-properties.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Saved Properties</span>
                        </div>
                        @if($savedCountBadge > 0)
                            <span
                                x-show="!sidebarCollapsed"
                                class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ request()->routeIs('saved-properties.*') ? 'bg-white text-blue-900' : 'bg-blue-50 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' }}"
                            >
                                {{ $savedCountBadge }}
                            </span>
                        @endif
                    </a>

                    <!-- Enquiries -->
                    <a
                        href="{{ route('enquiries.index') }}"
                        title="Property Enquiries"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center justify-between gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('enquiries.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('enquiries.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Enquiries</span>
                        </div>
                        @if($pendingEnquiriesBadge > 0)
                            <span
                                x-show="!sidebarCollapsed"
                                class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ request()->routeIs('enquiries.*') ? 'bg-white text-blue-900' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' }}"
                            >
                                {{ $pendingEnquiriesBadge }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Discover & Marketplace Section -->
                <div class="space-y-1 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <p
                        x-show="!sidebarCollapsed"
                        class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2"
                    >
                        Discover
                    </p>

                    <a
                        href="{{ route('recommendations.index') }}"
                        title="AI Recommendations"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-semibold transition group
                            {{ request()->routeIs('recommendations.*')
                                ? 'bg-blue-800 text-white shadow-xs'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-800 dark:hover:text-blue-400' }}"
                    >
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('recommendations.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">AI Recommendations</span>
                    </a>

                    <a
                        href="{{ route('properties.index') }}"
                        title="Browse Properties"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                        class="flex items-center gap-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 transition"
                    >
                        <svg class="w-5 h-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Browse Marketplace</span>
                    </a>
                </div>
            @endif
        </nav>

        <!-- Sidebar Bottom Controls: Theme Switcher & Logout -->
        <div class="p-3 border-t border-slate-200 dark:border-slate-800 space-y-1.5 bg-slate-50/60 dark:bg-slate-900/50">
            <!-- Theme Toggle Button inside Sidebar -->
            <button
                type="button"
                @click="toggleTheme()"
                :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3 justify-between'"
                class="w-full flex items-center gap-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800 transition"
            >
                <div class="flex items-center gap-3">
                    <!-- Sun Icon (shown in dark mode) -->
                    <svg x-show="darkMode" class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Moon Icon (shown in light mode) -->
                    <svg x-show="!darkMode" class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-text="darkMode ? 'Dark Theme' : 'Light Theme'"></span>
                </div>
                <span
                    x-show="!sidebarCollapsed"
                    class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200"
                    x-text="darkMode ? 'Dark' : 'Light'"
                ></span>
            </button>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    title="Sign Out"
                    :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'px-3'"
                    class="w-full flex items-center gap-3 py-2 rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="!sidebarCollapsed">Sign Out</span>
                </button>
            </form>
        </div>
    </aside>
@endif
