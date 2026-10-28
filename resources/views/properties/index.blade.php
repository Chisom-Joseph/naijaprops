@extends('layouts.app')

@section('title', 'Search & Filter Nigerian Properties – NaijaProps')

@section('content')
@php
    $activeFilters = array_filter([
        'search' => request('search'),
        'location' => request('location'),
        'property_type' => request('property_type'),
        'transaction_type' => request('transaction_type'),
        'price_range' => request('price_range'),
        'min_price' => request('min_price'),
        'max_price' => request('max_price'),
        'bedrooms' => request('bedrooms'),
        'status' => request('status'),
    ], fn($value) => !is_null($value) && $value !== '');
@endphp

<!-- Page Header & Advanced Search Filter Panel -->
<section class="bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-800/80 text-blue-100 border border-blue-600/40 mb-2">
                    Verified Nigerian Marketplace
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Find Your Ideal Property in Nigeria</h1>
                <p class="text-sm text-blue-100 mt-1">
                    Search and filter verified houses, duplexes, apartments, and land across Lagos, Abuja &amp; Port Harcourt.
                </p>
            </div>

            @auth
                @can('create', \App\Models\Property::class)
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('properties.my') }}" class="bg-blue-800/80 hover:bg-blue-700 text-white border border-blue-600 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                            My Listings
                        </a>
                        <a href="{{ route('properties.create') }}" class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            List a Property
                        </a>
                    </div>
                @endcan
            @endauth
        </div>

        <!-- Advanced Search & Filter Card -->
        <form method="GET" action="{{ route('properties.index') }}" class="bg-white text-slate-800 rounded-2xl p-4 sm:p-6 shadow-xl border border-blue-100">
            <!-- Quick Transaction Type Pills + City Shortcuts -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100">
                <div class="inline-flex items-center bg-slate-100 p-1 rounded-xl">
                    <a
                        href="{{ route('properties.index', request()->except(['transaction_type', 'page'])) }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ !request('transaction_type') ? 'bg-blue-800 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        All Listings
                    </a>
                    <a
                        href="{{ route('properties.index', array_merge(request()->except('page'), ['transaction_type' => 'sale'])) }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('transaction_type') === 'sale' ? 'bg-blue-800 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        For Sale
                    </a>
                    <a
                        href="{{ route('properties.index', array_merge(request()->except('page'), ['transaction_type' => 'rent'])) }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('transaction_type') === 'rent' ? 'bg-blue-800 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        For Rent
                    </a>
                </div>

                <!-- Quick Nigerian City Filter Links -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="text-slate-400 font-semibold mr-1">Popular Cities:</span>
                    @foreach(['Lagos', 'Abuja', 'Port Harcourt'] as $city)
                        <a
                            href="{{ route('properties.index', array_merge(request()->except('page'), ['location' => $city])) }}"
                            class="px-2.5 py-1 rounded-lg font-semibold border transition {{ request('location') === $city ? 'bg-blue-50 text-blue-800 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-800' }}"
                        >
                            {{ $city }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Primary Filter Grid: Keyword, Location, Property Type, Transaction Type, Price Range, Bedrooms -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5">
                <!-- 1. Keyword Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Keyword Search</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Title, estate, pool, C of O..."
                            class="w-full rounded-xl border border-slate-300 pl-9 pr-3.5 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none"
                        >
                    </div>
                </div>

                <!-- 2. Location Filter -->
                <div>
                    <label for="location" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Location</label>
                    <select
                        id="location"
                        name="location"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">All Locations</option>
                        @foreach(\App\Models\Property::LOCATIONS as $locValue => $locLabel)
                            <option value="{{ $locValue }}" @selected(request('location') === $locValue)>{{ $locLabel }}</option>
                        @endforeach
                        @if(request()->filled('location') && !array_key_exists(request('location'), \App\Models\Property::LOCATIONS))
                            <option value="{{ request('location') }}" selected>{{ request('location') }}</option>
                        @endif
                    </select>
                </div>

                <!-- 3. Property Type Filter -->
                <div>
                    <label for="property_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Property Type</label>
                    <select
                        id="property_type"
                        name="property_type"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">All Property Types</option>
                        @foreach(\App\Models\Property::PROPERTY_TYPES as $key => $label)
                            <option value="{{ $key }}" @selected(request('property_type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Price Range Filter (₦) -->
                <div>
                    <label for="price_range" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Price Range (₦)</label>
                    <select
                        id="price_range"
                        name="price_range"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Any Price Range</option>
                        @foreach(\App\Models\Property::PRICE_RANGES as $rangeKey => $rangeData)
                            <option value="{{ $rangeKey }}" @selected(request('price_range') === $rangeKey)>{{ $rangeData['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Bedrooms Filter -->
                <div>
                    <label for="bedrooms" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bedrooms</label>
                    <select
                        id="bedrooms"
                        name="bedrooms"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Any Bedrooms</option>
                        @foreach([1, 2, 3, 4, 5, 6] as $beds)
                            <option value="{{ $beds }}" @selected((string) request('bedrooms') === (string) $beds)>{{ $beds }}+ {{ \Illuminate\Support\Str::plural('Bedroom', $beds) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Secondary Filter Row: Transaction Type, Custom Min/Max Price (₦), Status, Sort & Submit -->
            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <!-- Transaction Type Select -->
                <div class="lg:col-span-2">
                    <label for="transaction_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Transaction Type</label>
                    <select
                        id="transaction_type"
                        name="transaction_type"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50 focus:bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Sale &amp; Rent</option>
                        <option value="sale" @selected(request('transaction_type') === 'sale')>For Sale</option>
                        <option value="rent" @selected(request('transaction_type') === 'rent')>For Rent</option>
                    </select>
                </div>

                <!-- Custom Min & Max Price in Naira (₦) -->
                <div class="lg:col-span-4">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Custom Budget in Naira (₦)</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="number"
                            name="min_price"
                            value="{{ request('min_price') }}"
                            placeholder="Min Price (₦)"
                            min="0"
                            aria-label="Minimum Price in Naira"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white px-3 py-2 text-xs focus:border-blue-700 focus:outline-none"
                        >
                        <span class="text-slate-400 text-xs font-bold">–</span>
                        <input
                            type="number"
                            name="max_price"
                            value="{{ request('max_price') }}"
                            placeholder="Max Price (₦)"
                            min="0"
                            aria-label="Maximum Price in Naira"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white px-3 py-2 text-xs focus:border-blue-700 focus:outline-none"
                        >
                    </div>
                </div>

                <!-- Availability Status -->
                <div class="lg:col-span-2">
                    <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Listing Status</label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50 focus:bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Order -->
                <div class="lg:col-span-2">
                    <label for="sort" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Sort By</label>
                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50 focus:bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest Listed</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High (₦)</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low (₦)</option>
                        <option value="bedrooms_desc" @selected(request('sort') === 'bedrooms_desc')>Most Bedrooms</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button
                        type="submit"
                        data-loading-text="Filtering..."
                        class="w-full bg-blue-800 hover:bg-blue-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Apply Filters
                    </button>
                    @if(!empty($activeFilters) || request('sort', 'latest') !== 'latest')
                        <a href="{{ route('properties.index') }}" data-skeleton-trigger class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl transition shrink-0">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Results Count Summary & Property Listings Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Results Bar: "X properties found" + Active Filter Pills -->
    <div class="bg-white rounded-2xl border border-slate-200 px-5 py-4 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                {{ number_format($properties->total()) }} {{ \Illuminate\Support\Str::plural('property', $properties->total()) }} found
            </h2>
            @if($properties->total() > 0)
                <span class="text-xs text-slate-500 font-medium">
                    (Showing {{ $properties->firstItem() }}–{{ $properties->lastItem() }})
                </span>
            @endif
        </div>

        @if(!empty($activeFilters))
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold text-slate-400">Active Filters:</span>

                @if(request()->filled('search'))
                    <a href="{{ route('properties.index', request()->except(['search', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        Keyword: "{{ request('search') }}"
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('location'))
                    <a href="{{ route('properties.index', request()->except(['location', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        Location: {{ request('location') }}
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('property_type') && isset(\App\Models\Property::PROPERTY_TYPES[request('property_type')]))
                    <a href="{{ route('properties.index', request()->except(['property_type', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        Type: {{ \App\Models\Property::PROPERTY_TYPES[request('property_type')] }}
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('transaction_type') && isset(\App\Models\Property::TRANSACTION_TYPES[request('transaction_type')]))
                    <a href="{{ route('properties.index', request()->except(['transaction_type', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        {{ \App\Models\Property::TRANSACTION_TYPES[request('transaction_type')] }}
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('price_range') && isset(\App\Models\Property::PRICE_RANGES[request('price_range')]))
                    <a href="{{ route('properties.index', request()->except(['price_range', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        Price: {{ \App\Models\Property::PRICE_RANGES[request('price_range')]['label'] }}
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('min_price') || request()->filled('max_price'))
                    <a href="{{ route('properties.index', request()->except(['min_price', 'max_price', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        Budget:
                        {{ request()->filled('min_price') ? '₦' . number_format((float) request('min_price')) : '₦0' }}
                        –
                        {{ request()->filled('max_price') ? '₦' . number_format((float) request('max_price')) : 'Any' }}
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('bedrooms'))
                    <a href="{{ route('properties.index', request()->except(['bedrooms', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        {{ request('bedrooms') }}+ Bedrooms
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                @if(request()->filled('status') && isset(\App\Models\Property::STATUSES[request('status')]))
                    <a href="{{ route('properties.index', request()->except(['status', 'page'])) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100 transition">
                        Status: {{ \App\Models\Property::STATUSES[request('status')] }}
                        <span class="text-blue-500 font-bold">&times;</span>
                    </a>
                @endif

                <a href="{{ route('properties.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 hover:underline ml-1">
                    Clear all
                </a>
            </div>
        @endif
    </div>

    <!-- Skeleton Loader for Property Cards Grid -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.property-card :count="6" />
    </div>

    <!-- Property Cards Grid -->
    <div data-np-content class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200">
        @forelse($properties as $property)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col group">
                <a href="{{ route('properties.show', $property) }}" class="relative h-56 bg-slate-200 block overflow-hidden">
                    <img
                        src="{{ $property->primary_image_url }}"
                        alt="{{ $property->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    >
                    <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm
                            {{ $property->transaction_type === 'sale' ? 'bg-blue-800 text-white' : 'bg-emerald-700 text-white' }}">
                            For {{ ucfirst($property->transaction_type) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/95 text-slate-800 shadow-sm">
                            {{ $property->property_type_label }}
                        </span>
                    </div>

                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase shadow-sm
                            @if($property->status === 'available') bg-emerald-500 text-white
                            @elseif($property->status === 'pending') bg-amber-500 text-white
                            @else bg-slate-800 text-white @endif">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>

                    @if($property->images_count > 1)
                        <div class="absolute bottom-3 right-3 bg-slate-900/75 text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg flex items-center gap-1 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $property->images_count }} Photos
                        </div>
                    @endif
                </a>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="text-xl font-extrabold text-blue-900">
                                {{ $property->formatted_price }}
                                @if($property->transaction_type === 'rent')
                                    <span class="text-xs font-normal text-slate-500">/ year</span>
                                @endif
                            </p>
                        </div>

                        <a href="{{ route('properties.show', $property) }}" class="block mt-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-800 transition line-clamp-1">
                                {{ $property->title }}
                            </h3>
                        </a>

                        <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $property->location }}
                        </p>

                        <p class="text-xs text-slate-600 mt-2 line-clamp-2">
                            {{ $property->description }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs text-slate-600 mb-3">
                            <div class="flex items-center gap-3">
                                @if(!is_null($property->bedrooms))
                                    <span class="flex items-center gap-1">
                                        <strong>{{ $property->bedrooms }}</strong> Beds
                                    </span>
                                @endif
                                @if(!is_null($property->bathrooms))
                                    <span class="flex items-center gap-1">
                                        <strong>{{ $property->bathrooms }}</strong> Baths
                                    </span>
                                @endif
                                @if(is_null($property->bedrooms) && is_null($property->bathrooms))
                                    <span class="text-slate-500">Land / Commercial</span>
                                @endif
                            </div>
                            <span class="text-[11px] text-slate-400 truncate max-w-[140px]">
                                {{ $property->owner?->full_name }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('properties.show', $property) }}" class="w-full text-center bg-slate-100 hover:bg-blue-800 hover:text-white text-slate-800 font-semibold text-xs py-2 px-3 rounded-xl transition">
                                View Property Details
                            </a>
                            @auth
                                @php($isCardSaved = in_array($property->id, $savedPropertyIds ?? [], true))
                                <form method="POST" action="{{ route('saved-properties.toggle', $property) }}" class="shrink-0">
                                    @csrf
                                    <button
                                        type="submit"
                                        title="{{ $isCardSaved ? 'Remove from Saved Properties' : 'Save Property' }}"
                                        aria-label="{{ $isCardSaved ? 'Remove from Saved Properties' : 'Save Property' }}"
                                        class="p-2 rounded-xl border transition {{ $isCardSaved ? 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100' : 'bg-slate-50 border-slate-200 text-slate-600 hover:text-red-600 hover:border-red-200' }}"
                                    >
                                        <svg class="w-4 h-4 {{ $isCardSaved ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </form>

                                @can('update', $property)
                                    <a href="{{ route('properties.edit', $property) }}" class="shrink-0 bg-blue-50 hover:bg-blue-100 text-blue-800 font-semibold text-xs py-2 px-3 rounded-xl border border-blue-200 transition">
                                        Edit
                                    </a>
                                @endcan
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No Matching Properties Found</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    We couldn't find any properties matching your search criteria. Try clearing some filters or exploring another Nigerian city.
                </p>
                <div class="mt-5 flex justify-center gap-3">
                    <a href="{{ route('properties.index') }}" class="bg-blue-800 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition">
                        Reset All Filters
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($properties->hasPages())
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    @endif

    <!-- AI-Assisted Recommendations Callout Banner on Search Page -->
    <div class="mt-10 bg-gradient-to-r from-blue-900 to-blue-800 rounded-2xl p-6 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-700/80 text-amber-300 border border-blue-500/40 mb-2">
                AI-Assisted Recommendations
            </span>
            <h3 class="text-lg font-extrabold">Want Ranked Property Suggestions with “Why Suggested” Explanations?</h3>
            <p class="text-xs text-blue-100 mt-1">
                Use our content-based recommendation engine to score listings by location similarity, property type, Naira budget, and bedrooms.
            </p>
        </div>
        <a href="{{ route('recommendations.index', array_filter([
            'location' => request('location'),
            'property_type' => request('property_type'),
            'transaction_type' => request('transaction_type'),
            'price_range' => request('price_range'),
            'bedrooms' => request('bedrooms'),
        ])) }}" class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs px-4 py-3 rounded-xl shadow-sm transition shrink-0 text-center">
            Open AI Recommendation Engine &rarr;
        </a>
    </div>
</section>
@endsection
