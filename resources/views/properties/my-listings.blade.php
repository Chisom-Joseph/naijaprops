@extends('layouts.app')

@section('title', 'My Property Listings – NaijaProps')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-700 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/15 text-blue-100 border border-white/20">
                    {{ auth()->user()->role_label }} Portfolio
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold mt-2">My Property Listings</h1>
                <p class="text-blue-100 text-sm mt-1">
                    Create, update, manage availability status, and upload photos for your properties.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" class="bg-blue-800/80 hover:bg-blue-800 text-white border border-blue-500/40 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    My Dashboard
                </a>
                <a href="{{ route('properties.create') }}" class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    List New Property
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Listings</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Available</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1">{{ $stats['available'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Offer</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sold / Rented</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-800 mt-1">{{ $stats['closed'] }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('properties.my') }}" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search your listings by title or location..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-700 focus:outline-none"
                >
            </div>
            <div>
                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" data-loading-text="Filtering..." class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded-xl transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'transaction_type']))
                    <a href="{{ route('properties.my') }}" data-skeleton-trigger class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Skeleton Loader for My Listings -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.list-items :items="4" variant="listing" />
    </div>

    <!-- Listings Table / Cards -->
    <div data-np-content class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-opacity duration-200">
        <div class="divide-y divide-slate-100">
            @forelse($properties as $property)
                <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-slate-50/70 transition">
                    <div class="flex items-start space-x-4">
                        <a href="{{ route('properties.show', $property) }}" class="shrink-0">
                            <img
                                src="{{ $property->primary_image_url }}"
                                alt="{{ $property->title }}"
                                class="w-28 h-20 rounded-xl object-cover bg-slate-100 border border-slate-200"
                            >
                        </a>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800">
                                    For {{ ucfirst($property->transaction_type) }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold uppercase rounded bg-slate-100 text-slate-700">
                                    {{ $property->property_type_label }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded
                                    @if($property->status === 'available') bg-emerald-100 text-emerald-800
                                    @elseif($property->status === 'pending') bg-amber-100 text-amber-800
                                    @else bg-slate-200 text-slate-700 @endif">
                                    {{ \App\Models\Property::STATUSES[$property->status] ?? ucfirst($property->status) }}
                                </span>
                            </div>

                            <a href="{{ route('properties.show', $property) }}" class="block mt-1">
                                <h2 class="text-base font-bold text-slate-900 hover:text-blue-800 transition truncate">
                                    {{ $property->title }}
                                </h2>
                            </a>

                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $property->location }}
                                @if(!is_null($property->bedrooms)) &bull; {{ $property->bedrooms }} Beds @endif
                                @if(!is_null($property->bathrooms)) &bull; {{ $property->bathrooms }} Baths @endif
                            </p>

                            <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] text-slate-500">
                                <span><strong>{{ $property->images_count }}</strong> Photos</span>
                                <span>&bull;</span>
                                <a href="{{ route('enquiries.index', ['tab' => 'received', 'property_id' => $property->id]) }}" class="text-blue-800 hover:underline font-medium">
                                    <strong>{{ $property->enquiries_count }}</strong> Enquiries
                                </a>
                                <span>&bull;</span>
                                <span><strong>{{ $property->saved_properties_count }}</strong> Saves</span>
                                <span>&bull;</span>
                                <span>Listed {{ $property->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end justify-between gap-3 shrink-0 border-t lg:border-t-0 pt-3 lg:pt-0 border-slate-100">
                        <p class="text-lg font-extrabold text-blue-900">
                            {{ $property->formatted_price }}
                            @if($property->transaction_type === 'rent')
                                <span class="text-xs font-normal text-slate-500">/ yr</span>
                            @endif
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Quick Status Update -->
                            <form method="POST" action="{{ route('properties.update-status', $property) }}">
                                @csrf
                                @method('PATCH')
                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    aria-label="Update status"
                                    class="text-xs font-semibold rounded-lg border border-slate-300 px-2.5 py-1.5 bg-white text-slate-700 focus:border-blue-600 focus:outline-none"
                                >
                                    @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}" @selected($property->status === $statusKey)>
                                            {{ $statusLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>

                            <a href="{{ route('properties.show', $property) }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition">
                                View
                            </a>

                            <a href="{{ route('properties.edit', $property) }}" class="text-xs font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-lg transition">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Delete this property listing permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-lg transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state
                    class="border-0 rounded-none shadow-none"
                    icon="property"
                    tone="blue"
                    badge="Portfolio Empty"
                    title="No Property Listings Found"
                    description="Start listing your verified houses, apartments, duplexes, or land in Lagos, Abuja, or Port Harcourt to connect with buyers and tenants."
                >
                    @if(request()->anyFilled(['search', 'status', 'transaction_type']))
                        <a href="{{ route('properties.my') }}" data-skeleton-trigger class="np-btn-interactive inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl transition">
                            <span>Reset Search Filters</span>
                        </a>
                    @endif
                    <a href="{{ route('properties.create') }}" class="np-btn-interactive inline-flex items-center gap-1.5 bg-blue-800 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Create Your First Listing</span>
                    </a>
                </x-empty-state>
            @endforelse
        </div>
    </div>

    @if($properties->hasPages())
        <div class="mt-6">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection
