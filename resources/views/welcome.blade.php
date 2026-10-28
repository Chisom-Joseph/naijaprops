@extends('layouts.app')

@section('title', 'NaijaProps – Nigerian Property Listing & Trading System')

@section('content')
<!-- Hero Section with Search Bar + Filters -->
<section class="bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 text-white py-14 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-8">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-800/80 text-blue-100 border border-blue-600/40 mb-4">
                Nigeria's Centralised Property Platform
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                Find Verified Properties in <span class="text-blue-300">Lagos, Abuja &amp; Port Harcourt</span>
            </h1>
            <p class="mt-4 text-base sm:text-lg text-blue-100 leading-relaxed">
                Buy, rent, or list houses, duplexes, apartments, and land with transparent pricing in Naira (₦), direct owner/agent enquiries, and AI-assisted recommendations.
            </p>
        </div>

        <!-- Hero Search & Filter Card -->
        <div class="bg-white text-slate-800 rounded-2xl p-4 sm:p-6 shadow-2xl border border-blue-100">
            <form method="GET" action="{{ route('properties.index') }}">
                <!-- Transaction Type Tabs -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2" x-data="{ tx: '' }">
                        <input type="hidden" name="transaction_type" :value="tx">
                        <button
                            type="button"
                            @click="tx = ''"
                            :class="tx === '' ? 'bg-blue-800 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold transition"
                        >
                            All Properties
                        </button>
                        <button
                            type="button"
                            @click="tx = 'sale'"
                            :class="tx === 'sale' ? 'bg-blue-800 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold transition"
                        >
                            For Sale
                        </button>
                        <button
                            type="button"
                            @click="tx = 'rent'"
                            :class="tx === 'rent' ? 'bg-blue-800 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold transition"
                        >
                            For Rent
                        </button>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                        <span class="font-semibold text-slate-400">Popular:</span>
                        <a href="{{ route('properties.index', ['location' => 'Lagos']) }}" class="hover:text-blue-800 font-semibold">Lagos</a>
                        <span>&bull;</span>
                        <a href="{{ route('properties.index', ['location' => 'Abuja']) }}" class="hover:text-blue-800 font-semibold">Abuja</a>
                        <span>&bull;</span>
                        <a href="{{ route('properties.index', ['location' => 'Port Harcourt']) }}" class="hover:text-blue-800 font-semibold">Port Harcourt</a>
                    </div>
                </div>

                <!-- Search & Filters Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Keyword Search -->
                    <div>
                        <label for="hero_search" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Keyword</label>
                        <input
                            type="text"
                            id="hero_search"
                            name="search"
                            placeholder="e.g. Duplex, Pool, Lekki..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none"
                        >
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label for="hero_location" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Location</label>
                        <select
                            id="hero_location"
                            name="location"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            <option value="">All Locations</option>
                            @foreach(\App\Models\Property::LOCATIONS as $locValue => $locLabel)
                                <option value="{{ $locValue }}">{{ $locLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Property Type Filter -->
                    <div>
                        <label for="hero_property_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Property Type</label>
                        <select
                            id="hero_property_type"
                            name="property_type"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            <option value="">All Types</option>
                            @foreach(\App\Models\Property::PROPERTY_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range Filter (₦) -->
                    <div>
                        <label for="hero_price_range" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Price Range (₦)</label>
                        <select
                            id="hero_price_range"
                            name="price_range"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            <option value="">Any Price</option>
                            @foreach(\App\Models\Property::PRICE_RANGES as $rangeKey => $rangeData)
                                <option value="{{ $rangeKey }}">{{ $rangeData['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bedrooms Filter -->
                    <div>
                        <label for="hero_bedrooms" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bedrooms</label>
                        <select
                            id="hero_bedrooms"
                            name="bedrooms"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                        >
                            <option value="">Any Beds</option>
                            @foreach([1, 2, 3, 4, 5, 6] as $beds)
                                <option value="{{ $beds }}">{{ $beds }}+ Bedrooms</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-800 hover:bg-blue-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search Properties
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Properties Preview -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Featured Nigerian Properties</h2>
            <p class="text-sm text-slate-600 mt-1">Verified listings from property owners and agents across major Nigerian cities</p>
        </div>
        <a href="{{ route('properties.index') }}" class="text-sm font-bold text-blue-800 hover:text-blue-700 inline-flex items-center gap-1">
            View All Properties &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($featuredProperties ?? [] as $property)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col group">
                <a href="{{ route('properties.show', $property) }}" class="relative h-52 bg-slate-200 block overflow-hidden">
                    <img
                        src="{{ $property->primary_image_url }}"
                        alt="{{ $property->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    >
                    <div class="absolute top-3 left-3 flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm
                            {{ $property->transaction_type === 'sale' ? 'bg-blue-800 text-white' : 'bg-emerald-700 text-white' }}">
                            For {{ ucfirst($property->transaction_type) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/95 text-slate-800 shadow-sm">
                            {{ $property->property_type_label }}
                        </span>
                    </div>
                </a>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <p class="text-xl font-extrabold text-blue-900">
                            {{ $property->formatted_price }}
                            @if($property->transaction_type === 'rent')
                                <span class="text-xs font-normal text-slate-500">/ year</span>
                            @endif
                        </p>
                        <a href="{{ route('properties.show', $property) }}" class="block mt-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-800 transition line-clamp-1">{{ $property->title }}</h3>
                        </a>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $property->location }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <div class="flex items-center gap-3">
                            @if($property->bedrooms)
                                <span><strong>{{ $property->bedrooms }}</strong> Beds</span>
                            @endif
                            @if($property->bathrooms)
                                <span><strong>{{ $property->bathrooms }}</strong> Baths</span>
                            @endif
                            @if(!$property->bedrooms && !$property->bathrooms)
                                <span>Residential Plot</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @auth
                                @php($isFeaturedSaved = in_array($property->id, $savedPropertyIds ?? [], true))
                                <form method="POST" action="{{ route('saved-properties.toggle', $property) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        title="{{ $isFeaturedSaved ? 'Remove from Saved Properties' : 'Save Property' }}"
                                        class="p-1.5 rounded-lg border transition {{ $isFeaturedSaved ? 'bg-red-50 border-red-200 text-red-600' : 'bg-slate-50 border-slate-200 text-slate-500 hover:text-red-600' }}"
                                    >
                                        <svg class="w-3.5 h-3.5 {{ $isFeaturedSaved ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endauth
                            <a href="{{ route('properties.show', $property) }}" class="text-xs font-bold text-blue-800 hover:underline">
                                Details &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-10 text-center">
                <p class="text-slate-600 font-medium">No properties seeded yet. Run <code class="bg-slate-100 px-2 py-0.5 rounded text-xs">php artisan migrate --seed</code> to populate Nigerian sample listings.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
