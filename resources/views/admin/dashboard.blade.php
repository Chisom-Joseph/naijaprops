@extends('layouts.app')

@section('title', 'Admin Dashboard – NaijaProps')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Admin Header Banner -->
    <div class="bg-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8 border border-slate-800">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-200 border border-purple-400/30">
                        System Administrator
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold">NaijaProps Admin Control Center</h1>
                <p class="text-slate-300 text-sm mt-1">
                    Monitor platform statistics, manage user roles &amp; account status, approve/reject property listings, and resolve reports.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.reports.index') }}" class="bg-red-600 hover:bg-red-500 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm transition inline-flex items-center gap-2">
                    <span>Review Reports</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-extrabold bg-white/20">
                        {{ $stats['pending_reports'] }} Pending
                    </span>
                </a>
                <a href="{{ route('admin.properties.index') }}" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    Manage Properties ({{ $stats['total_properties'] }})
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-blue-700 hover:bg-blue-600 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                    Manage Users ({{ $stats['total_users'] }})
                </a>
            </div>
        </div>
    </div>

    <!-- Primary Key Metrics (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Users</p>
                <a href="{{ route('admin.users.index') }}" class="text-[11px] font-semibold text-blue-800 hover:underline">Manage &rarr;</a>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-emerald-700 font-medium mt-1">
                {{ $stats['active_users'] }} active &bull; {{ $stats['inactive_users'] }} inactive
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Property Providers</p>
                <a href="{{ route('admin.users.index', ['role' => 'agent']) }}" class="text-[11px] font-semibold text-blue-800 hover:underline">Filter &rarr;</a>
            </div>
            <p class="text-3xl font-extrabold text-blue-800 mt-2">{{ $stats['owners_and_agents'] }}</p>
            <p class="text-xs text-slate-500 mt-1">
                {{ $stats['buyers'] }} buyers/tenants &bull; {{ $stats['admins'] }} {{ \Illuminate\Support\Str::plural('admin', $stats['admins']) }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Properties</p>
                <a href="{{ route('admin.properties.index') }}" class="text-[11px] font-semibold text-blue-800 hover:underline">Moderate &rarr;</a>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['total_properties'] }}</p>
            <p class="text-xs text-emerald-700 font-medium mt-1">
                {{ $stats['available_properties'] }} available &bull; {{ $stats['pending_properties'] }} pending &bull; {{ $stats['closed_properties'] }} closed
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Listing Reports</p>
                <a href="{{ route('admin.reports.index') }}" class="text-[11px] font-semibold text-red-700 hover:underline">Review All &rarr;</a>
            </div>
            <p class="text-3xl font-extrabold {{ $stats['pending_reports'] > 0 ? 'text-red-600' : 'text-slate-900' }} mt-2">
                {{ $stats['pending_reports'] }} <span class="text-sm font-semibold text-slate-500">pending</span>
            </p>
            <p class="text-xs text-slate-500 mt-1">
                {{ $stats['total_reports'] }} total ({{ $stats['reviewed_reports'] }} reviewed, {{ $stats['resolved_reports'] }} resolved)
            </p>
        </div>
    </div>

    <!-- Secondary Platform Statistics Strip (Market & Regional Breakdown) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nigerian Market Coverage</p>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs font-semibold text-slate-700">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-100">
                        Lagos: {{ $stats['lagos_properties'] }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-100">
                        Abuja: {{ $stats['abuja_properties'] }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-100">
                        Port Harcourt: {{ $stats['ph_properties'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaction Split</p>
                <div class="flex items-center gap-3 mt-2 text-xs font-semibold">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-900 border border-blue-200">
                        For Sale: {{ $stats['sale_properties'] }} Listings
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-900 border border-emerald-200">
                        For Rent: {{ $stats['rent_properties'] }} Listings
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Buyer Engagement</p>
                <div class="flex items-center gap-3 mt-2 text-xs font-semibold text-slate-700">
                    <span><strong>{{ $stats['total_enquiries'] }}</strong> Enquiries ({{ $stats['pending_enquiries'] }} pending)</span>
                    <span>&bull;</span>
                    <span><strong>{{ $stats['total_saved'] }}</strong> Saved Bookmarks</span>
                </div>
            </div>
            <a href="{{ route('enquiries.index') }}" class="text-xs font-semibold text-blue-800 hover:underline shrink-0">
                Enquiries &rarr;
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Columns: Recent Users & Recent Properties Moderation -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recent Property Listings Moderation Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Recent Property Listings</h2>
                        <p class="text-xs text-slate-500">Approve, place on hold, or edit newly listed properties</p>
                    </div>
                    <a href="{{ route('admin.properties.index') }}" class="text-xs font-semibold text-blue-800 hover:underline">
                        Manage All Properties &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recentProperties as $property)
                        <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-extrabold text-blue-900">{{ $property->formatted_price }}</span>
                                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                        {{ $property->location }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded
                                        @if($property->status === 'available') bg-emerald-100 text-emerald-800
                                        @elseif($property->status === 'pending') bg-amber-100 text-amber-800
                                        @else bg-slate-200 text-slate-700 @endif">
                                        {{ \App\Models\Property::STATUSES[$property->status] ?? ucfirst($property->status) }}
                                    </span>
                                    @if($property->reports_count > 0)
                                        <a href="{{ route('admin.reports.index', ['property_id' => $property->id]) }}" class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-100 text-red-800">
                                            {{ $property->reports_count }} {{ \Illuminate\Support\Str::plural('Report', $property->reports_count) }}
                                        </a>
                                    @endif
                                </div>
                                <a href="{{ route('properties.show', $property) }}" class="text-sm font-bold text-slate-900 hover:text-blue-800 mt-1 block truncate">
                                    {{ $property->title }}
                                </a>
                                <p class="text-xs text-slate-500">
                                    Listed by <span class="font-medium text-slate-700">{{ $property->owner?->full_name }}</span> ({{ $property->owner?->role_label }})
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                                @if($property->status !== 'available')
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition">
                                            Approve
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-[11px] font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2.5 py-1 rounded-lg transition">
                                            Hold
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('properties.edit', $property) }}" class="text-[11px] font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg transition">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-500">
                            No properties listed yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recently Registered Users Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Recently Registered Users</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-blue-800 hover:underline">
                        View &amp; Manage All &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Quick Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($recentUsers as $u)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4">
                                        <p class="font-semibold text-slate-900">{{ $u->full_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $u->email }}</p>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            @if($u->role === 'admin') bg-purple-100 text-purple-800
                                            @elseif($u->role === 'owner') bg-blue-100 text-blue-800
                                            @elseif($u->role === 'agent') bg-indigo-100 text-indigo-800
                                            @else bg-slate-100 text-slate-700 @endif">
                                            {{ $u->role_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $u->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'active' ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                            {{ ucfirst($u->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if(auth()->id() !== $u->id)
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $u) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border transition
                                                    {{ $u->status === 'active'
                                                        ? 'border-red-200 text-red-700 hover:bg-red-50'
                                                        : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                                    {{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Current Admin</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Active Reports Queue -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Active Listing Reports</h2>
                        <p class="text-[11px] text-slate-500">Pending &amp; under-review user reports</p>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-blue-800 hover:underline">
                        All Reports &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($pendingReports as $report)
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                @if($report->property)
                                    <a href="{{ route('properties.show', $report->property) }}" class="text-xs font-bold text-slate-900 hover:text-blue-800 line-clamp-1">
                                        {{ $report->property->title }}
                                    </a>
                                @else
                                    <span class="text-xs font-bold text-slate-400 italic">Deleted Listing</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0
                                    {{ $report->status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $report->status_label }}
                                </span>
                            </div>

                            <p class="text-xs text-red-800 bg-red-50 p-2.5 rounded-xl border border-red-100 leading-relaxed">
                                {{ $report->reason }}
                            </p>

                            <p class="text-[11px] text-slate-500">
                                Reported by <span class="font-semibold text-slate-700">{{ $report->reporter?->full_name }}</span>
                                &bull; {{ $report->created_at?->diffForHumans() }}
                            </p>

                            <div class="pt-1 flex flex-wrap items-center gap-1.5">
                                @if($report->status === 'pending')
                                    <form method="POST" action="{{ route('admin.reports.update-status', $report) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="reviewed">
                                        <input type="hidden" name="property_action" value="none">
                                        <button type="submit" class="text-[11px] font-semibold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-2.5 py-1 rounded-lg transition">
                                            Mark Reviewed
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.reports.update-status', $report) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="resolved">
                                    <input type="hidden" name="property_action" value="none">
                                    <button type="submit" class="text-[11px] font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 px-2.5 py-1 rounded-lg transition">
                                        Resolve
                                    </button>
                                </form>

                                @if($report->property && $report->property->status !== 'pending')
                                    <form method="POST" action="{{ route('admin.reports.update-status', $report) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="reviewed">
                                        <input type="hidden" name="property_action" value="reject">
                                        <button type="submit" class="text-[11px] font-semibold bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg transition">
                                            Hold Listing
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-500">
                            No pending or active reports at this time.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
