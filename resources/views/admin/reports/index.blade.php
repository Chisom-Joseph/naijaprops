@extends('layouts.app')

@section('title', 'Manage Property Reports – Admin – NaijaProps')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Admin Header Banner -->
    <div class="bg-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8 border border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-200 border border-red-400/30">
                        Trust &amp; Safety Moderation
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold">Suspicious Listing Reports</h1>
                <p class="text-slate-300 text-sm mt-1">
                    Review user-submitted reports, investigate flagged Nigerian listings, and approve, place on hold, or remove properties.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    &larr; Admin Dashboard
                </a>
                <a href="{{ route('admin.properties.index', ['flagged' => 1]) }}" class="bg-blue-700 hover:bg-blue-600 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                    Flagged Properties ({{ $stats['flagged_properties'] }})
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Reports</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Review</p>
            <p class="text-2xl sm:text-3xl font-extrabold {{ $stats['pending'] > 0 ? 'text-red-600' : 'text-slate-900' }} mt-1">
                {{ $stats['pending'] }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Under Review</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['reviewed'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Resolved</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1">{{ $stats['resolved'] }}</p>
        </div>
        <div class="col-span-2 lg:col-span-1 bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Flagged Listings</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-800 mt-1">{{ $stats['flagged_properties'] }}</p>
        </div>
    </div>

    @if($filteredProperty)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="text-xs text-blue-900">
                Showing reports specifically for listing:
                <a href="{{ route('properties.show', $filteredProperty) }}" class="font-bold underline hover:text-blue-700">
                    {{ $filteredProperty->title }} ({{ $filteredProperty->location }})
                </a>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-blue-800 bg-white border border-blue-200 px-3 py-1.5 rounded-xl hover:bg-blue-100 transition self-start sm:self-auto">
                Clear Property Filter &times;
            </a>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        @if(request('property_id'))
            <input type="hidden" name="property_id" value="{{ request('property_id') }}">
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label for="search" class="sr-only">Search reports</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by report reason, property title/location, reporter, or owner..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-700 focus:outline-none"
                >
            </div>
            <div>
                <label for="status" class="sr-only">Report Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none"
                >
                    <option value="">All Report Statuses</option>
                    @foreach(\App\Models\Report::STATUSES as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" data-loading-text="Filtering..." class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded-xl transition">
                    Filter Reports
                </button>
                @if(request()->anyFilled(['search', 'status', 'property_id']))
                    <a href="{{ route('admin.reports.index') }}" data-skeleton-trigger class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Skeleton Loader for Reports List -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.list-items :items="3" variant="enquiry" />
    </div>

    <!-- Reports List -->
    <div data-np-content class="space-y-4 transition-opacity duration-200">
        @forelse($reports as $report)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 hover:border-slate-300 transition">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <!-- Left Column: Property Info + Report Details -->
                    <div class="flex-1 space-y-4">
                        <!-- Top Badges & Report Meta -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-bold text-slate-400">#RP-{{ str_pad((string) $report->id, 4, '0', STR_PAD_LEFT) }}</span>

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($report->status === 'pending') bg-red-100 text-red-800
                                    @elseif($report->status === 'reviewed') bg-amber-100 text-amber-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        @if($report->status === 'pending') bg-red-600
                                        @elseif($report->status === 'reviewed') bg-amber-600
                                        @else bg-emerald-600 @endif"></span>
                                    {{ $report->status_label }}
                                </span>

                                <span class="text-xs text-slate-500">
                                    Submitted {{ $report->created_at?->format('M d, Y • h:i A') }} ({{ $report->created_at?->diffForHumans() }})
                                </span>
                            </div>
                        </div>

                        <!-- Reported Property Card Summary -->
                        @if($report->property)
                            <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                <a href="{{ route('properties.show', $report->property) }}" class="shrink-0">
                                    <img
                                        src="{{ $report->property->primary_image_url }}"
                                        alt="{{ $report->property->title }}"
                                        class="w-20 h-16 rounded-lg object-cover border border-slate-200 bg-slate-100"
                                    >
                                </a>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('properties.show', $report->property) }}" class="text-sm font-bold text-slate-900 hover:text-blue-800 truncate">
                                            {{ $report->property->title }}
                                        </a>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                            @if($report->property->status === 'available') bg-emerald-100 text-emerald-800
                                            @elseif($report->property->status === 'pending') bg-amber-100 text-amber-800
                                            @else bg-slate-200 text-slate-700 @endif">
                                            Listing: {{ \App\Models\Property::STATUSES[$report->property->status] ?? ucfirst($report->property->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        <span class="font-bold text-blue-900">{{ $report->property->formatted_price }}</span>
                                        &bull; {{ $report->property->location }}
                                        &bull; {{ $report->property->property_type_label }} (For {{ ucfirst($report->property->transaction_type) }})
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        Listed by: <strong class="text-slate-700">{{ $report->property->owner?->full_name }}</strong>
                                        ({{ $report->property->owner?->role_label }} • {{ $report->property->owner?->email }})
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-500 italic">
                                The reported property has already been removed.
                            </div>
                        @endif

                        <!-- Report Reason Box -->
                        <div class="p-4 rounded-xl bg-red-50/70 border border-red-200/80">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-red-800 mb-1">
                                Reason for Report:
                            </p>
                            <p class="text-sm text-slate-900 leading-relaxed whitespace-pre-line">{{ $report->reason }}</p>
                            <div class="mt-2.5 pt-2 border-t border-red-200/60 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600">
                                <span>
                                    Reported by:
                                    <strong class="text-slate-800">{{ $report->reporter?->full_name ?? 'Unknown User' }}</strong>
                                    @if($report->reporter?->email)
                                        ({{ $report->reporter->email }}{{ $report->reporter->phone ? ' • ' . $report->reporter->phone : '' }})
                                    @endif
                                </span>
                                <span class="text-[11px] font-medium text-slate-500">
                                    Reporter Role: {{ $report->reporter?->role_label ?? 'User' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Admin Review & Moderation Actions -->
                    <div class="lg:w-80 shrink-0 bg-slate-50 rounded-xl border border-slate-200 p-4 space-y-3.5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            Admin Moderation Action
                        </h3>

                        <form method="POST" action="{{ route('admin.reports.update-status', $report) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="report-status-{{ $report->id }}" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                    Report Status
                                </label>
                                <select
                                    id="report-status-{{ $report->id }}"
                                    name="status"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold bg-white focus:border-blue-700 focus:outline-none"
                                >
                                    @foreach(\App\Models\Report::STATUSES as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}" @selected($report->status === $statusKey)>
                                            {{ $statusLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($report->property)
                                <div>
                                    <label for="property-action-{{ $report->id }}" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                        Action on Property Listing
                                    </label>
                                    <select
                                        id="property-action-{{ $report->id }}"
                                        name="property_action"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium bg-white focus:border-blue-700 focus:outline-none"
                                    >
                                        <option value="none">Keep current listing status ({{ \App\Models\Property::STATUSES[$report->property->status] ?? $report->property->status }})</option>
                                        <option value="approve">Approve Listing (Set Available)</option>
                                        <option value="reject">Reject / Suspend Listing (Set Pending Hold)</option>
                                        <option value="remove">Remove / Delete Listing Permanently</option>
                                    </select>
                                </div>
                            @endif

                            <button
                                type="submit"
                                data-loading-text="Applying decision..."
                                class="w-full bg-blue-800 hover:bg-blue-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-sm transition"
                            >
                                Apply Moderation Decision
                            </button>
                        </form>

                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-2">
                            @if($report->property)
                                <div class="flex items-center gap-1.5">
                                    <a
                                        href="{{ route('properties.show', $report->property) }}"
                                        class="text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 px-2.5 py-1.5 rounded-lg transition"
                                    >
                                        Inspect Listing
                                    </a>
                                    <a
                                        href="{{ route('properties.edit', $report->property) }}"
                                        class="text-xs font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1.5 rounded-lg transition"
                                    >
                                        Edit Listing
                                    </a>
                                </div>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('admin.reports.destroy', $report) }}"
                                onsubmit="return confirm('Dismiss and delete this report record?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-xs font-semibold text-red-700 hover:text-red-800 bg-red-50 hover:bg-red-100 border border-red-200 px-2.5 py-1.5 rounded-lg transition"
                                >
                                    Delete Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No Property Reports Found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    There are no property reports matching your current filter criteria.
                </p>
            </div>
        @endforelse
    </div>

    @if($reports->hasPages())
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
