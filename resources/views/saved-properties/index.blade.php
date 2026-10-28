@extends('layouts.app')

@section('title', 'Saved Properties – NaijaProps')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-700 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/15 text-blue-100 border border-white/20">
                    Personal Watchlist
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold mt-2">My Saved Properties</h1>
                <p class="text-blue-100 text-sm mt-1">
                    Compare your bookmarked Nigerian houses, apartments, duplexes, and land before sending an enquiry.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" class="bg-blue-800/80 hover:bg-blue-800 text-white border border-blue-500/40 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    My Dashboard
                </a>
                <a href="{{ route('properties.index') }}" class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                    Browse More Properties
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Bookmarked</p>
            <p class="text-3xl font-extrabold text-blue-900 mt-1">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Properties saved to your account</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Saved For Sale</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['for_sale'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Outright purchase listings</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Saved For Rent</p>
            <p class="text-3xl font-extrabold text-emerald-700 mt-1">{{ $stats['for_rent'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Annual rental listings</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('saved-properties.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search your saved properties by title or location..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-700 focus:outline-none"
                >
            </div>
            <div>
                <select name="transaction_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none">
                    <option value="">Sale &amp; Rent</option>
                    <option value="sale" @selected(request('transaction_type') === 'sale')>For Sale</option>
                    <option value="rent" @selected(request('transaction_type') === 'rent')>For Rent</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" data-loading-text="Filtering..." class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded-xl transition">
                    Filter Saved
                </button>
                @if(request()->anyFilled(['search', 'transaction_type']))
                    <a href="{{ route('saved-properties.index') }}" data-skeleton-trigger class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Skeleton Loader for Saved Properties Grid -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.property-card :count="3" />
    </div>

    <!-- Saved Properties Grid -->
    <div data-np-content class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200">
        @forelse($savedProperties as $saved)
            @php($property = $saved->property)
            @if($property)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col group">
                    <div class="relative h-52 bg-slate-200 overflow-hidden">
                        <a href="{{ route('properties.show', $property) }}" class="block w-full h-full">
                            <img
                                src="{{ $property->primary_image_url }}"
                                alt="{{ $property->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            >
                        </a>

                        <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm
                                {{ $property->transaction_type === 'sale' ? 'bg-blue-800 text-white' : 'bg-emerald-700 text-white' }}">
                                For {{ ucfirst($property->transaction_type) }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/95 text-slate-800 shadow-sm">
                                {{ $property->property_type_label }}
                            </span>
                        </div>

                        <!-- Remove from Saved Button -->
                        <form method="POST" action="{{ route('saved-properties.destroy', $property) }}" class="absolute top-3 right-3">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                title="Remove from Saved Properties"
                                class="w-9 h-9 rounded-full bg-white/95 hover:bg-red-50 text-red-600 flex items-center justify-center shadow-sm transition"
                            >
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>

                        <div class="absolute bottom-3 left-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase shadow-sm
                                @if($property->status === 'available') bg-emerald-500 text-white
                                @elseif($property->status === 'pending') bg-amber-500 text-white
                                @else bg-slate-800 text-white @endif">
                                {{ ucfirst($property->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-xl font-extrabold text-blue-900">
                                    {{ $property->formatted_price }}
                                    @if($property->transaction_type === 'rent')
                                        <span class="text-xs font-normal text-slate-500">/ year</span>
                                    @endif
                                </p>
                                <span class="text-[11px] text-slate-400">Saved {{ $saved->created_at?->diffForHumans() }}</span>
                            </div>

                            <a href="{{ route('properties.show', $property) }}" class="block mt-1">
                                <h2 class="text-base font-bold text-slate-900 group-hover:text-blue-800 transition line-clamp-1">
                                    {{ $property->title }}
                                </h2>
                            </a>

                            <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $property->location }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between text-xs text-slate-600 mb-3">
                                <div class="flex items-center gap-3">
                                    @if(!is_null($property->bedrooms))
                                        <span><strong>{{ $property->bedrooms }}</strong> Beds</span>
                                    @endif
                                    @if(!is_null($property->bathrooms))
                                        <span><strong>{{ $property->bathrooms }}</strong> Baths</span>
                                    @endif
                                    @if(is_null($property->bedrooms) && is_null($property->bathrooms))
                                        <span class="text-slate-500">Land / Plot</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 truncate max-w-[140px]">
                                    {{ $property->owner?->full_name }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('properties.show', $property) }}" class="flex-1 text-center bg-blue-800 hover:bg-blue-700 text-white font-semibold text-xs py-2.5 px-3 rounded-xl transition">
                                    View &amp; Enquire
                                </a>
                                <form method="POST" action="{{ route('saved-properties.destroy', $property) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold text-xs py-2.5 px-3 rounded-xl transition">
                                        Unsave
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No Saved Properties Yet</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Bookmark properties across Lagos, Abuja, and Port Harcourt while browsing to easily compare prices and features here.
                </p>
                <a href="{{ route('properties.index') }}" class="mt-5 inline-flex items-center gap-1.5 bg-blue-800 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                    Browse Properties
                </a>
            </div>
        @endforelse
    </div>

    @if($savedProperties->hasPages())
        <div class="mt-8">
            {{ $savedProperties->links() }}
        </div>
    @endif
</div>
@endsection
