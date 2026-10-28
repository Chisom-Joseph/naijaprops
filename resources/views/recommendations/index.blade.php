@extends('layouts.app')

@section('title', 'AI-Assisted Property Recommendations – NaijaProps')

@section('content')
@php
    $hasCustomPreferences = !empty(array_filter($preferences ?? [], fn($v) => !is_null($v) && $v !== ''));
@endphp

<!-- AI Recommendation Header & Interactive Preference Form -->
<section class="bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 text-white py-10 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-800/80 text-blue-100 border border-blue-600/40 mb-3">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Content-Based AI Recommendation Engine
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    AI-Assisted Property Recommendations
                </h1>
                <p class="mt-2 text-sm sm:text-base text-blue-100 leading-relaxed">
                    Our content-based recommendation engine ranks available Nigerian properties by scoring location similarity, property type, Naira (₦) price range, and bedroom count—complete with transparent <strong>“Why suggested”</strong> explanations.
                </p>
            </div>

            <!-- Scoring Model Weights Card -->
            <div class="bg-blue-900/60 border border-blue-700/60 rounded-2xl p-4 sm:p-5 shrink-0 lg:w-80">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-200 mb-3">Content Scoring Weights (100 Pts)</p>
                <div class="grid grid-cols-2 gap-2.5 text-xs">
                    <div class="bg-blue-950/60 rounded-xl p-2.5 border border-blue-800/60">
                        <span class="text-amber-300 font-extrabold text-sm">35 pts</span>
                        <p class="text-blue-100 font-medium mt-0.5">Location Match</p>
                    </div>
                    <div class="bg-blue-950/60 rounded-xl p-2.5 border border-blue-800/60">
                        <span class="text-amber-300 font-extrabold text-sm">25 pts</span>
                        <p class="text-blue-100 font-medium mt-0.5">Property Type</p>
                    </div>
                    <div class="bg-blue-950/60 rounded-xl p-2.5 border border-blue-800/60">
                        <span class="text-amber-300 font-extrabold text-sm">25 pts</span>
                        <p class="text-blue-100 font-medium mt-0.5">Price Range (₦)</p>
                    </div>
                    <div class="bg-blue-950/60 rounded-xl p-2.5 border border-blue-800/60">
                        <span class="text-amber-300 font-extrabold text-sm">15 pts</span>
                        <p class="text-blue-100 font-medium mt-0.5">Bedrooms Match</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preference Customization Card -->
        <form method="GET" action="{{ route('recommendations.index') }}" class="bg-white text-slate-800 rounded-2xl p-4 sm:p-6 shadow-xl border border-blue-100">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Customize Recommendation Preferences</h2>
                    <p class="text-xs text-slate-500">
                        Select your ideal criteria below or leave blank to rank properties automatically from your saved listings &amp; enquiries.
                    </p>
                </div>
                @if($hasCustomPreferences)
                    <a href="{{ route('recommendations.index') }}" class="text-xs font-bold text-blue-800 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-xl transition">
                        Reset to My Activity Profile
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5">
                <!-- 1. Location -->
                <div>
                    <label for="rec_location" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Preferred Location</label>
                    <select
                        id="rec_location"
                        name="location"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Auto / Any Location</option>
                        @foreach(\App\Models\Property::LOCATIONS as $locValue => $locLabel)
                            <option value="{{ $locValue }}" @selected(($preferences['location'] ?? '') === $locValue)>{{ $locLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Property Type -->
                <div>
                    <label for="rec_property_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Property Type</label>
                    <select
                        id="rec_property_type"
                        name="property_type"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Auto / Any Type</option>
                        @foreach(\App\Models\Property::PROPERTY_TYPES as $key => $label)
                            <option value="{{ $key }}" @selected(($preferences['property_type'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Transaction Type -->
                <div>
                    <label for="rec_transaction_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Transaction Type</label>
                    <select
                        id="rec_transaction_type"
                        name="transaction_type"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Sale &amp; Rent</option>
                        @foreach(\App\Models\Property::TRANSACTION_TYPES as $txKey => $txLabel)
                            <option value="{{ $txKey }}" @selected(($preferences['transaction_type'] ?? '') === $txKey)>{{ $txLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Price Range (₦) -->
                <div>
                    <label for="rec_price_range" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Target Price Range (₦)</label>
                    <select
                        id="rec_price_range"
                        name="price_range"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Auto / Any Budget</option>
                        @foreach(\App\Models\Property::PRICE_RANGES as $rangeKey => $rangeData)
                            <option value="{{ $rangeKey }}" @selected(($preferences['price_range'] ?? '') === $rangeKey)>{{ $rangeData['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Bedrooms -->
                <div>
                    <label for="rec_bedrooms" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Ideal Bedrooms</label>
                    <select
                        id="rec_bedrooms"
                        name="bedrooms"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-blue-700 focus:outline-none"
                    >
                        <option value="">Auto / Any Beds</option>
                        @foreach([1, 2, 3, 4, 5, 6] as $beds)
                            <option value="{{ $beds }}" @selected((string) ($preferences['bedrooms'] ?? '') === (string) $beds)>{{ $beds }} {{ \Illuminate\Support\Str::plural('Bedroom', $beds) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Submit Button -->
                <div class="flex items-end">
                    <button
                        type="submit"
                        data-loading-text="Ranking..."
                        class="w-full bg-blue-800 hover:bg-blue-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Rank Properties
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Active Profile Summary & Ranked Results Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Active Profile Signals Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-8 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-900">
                    Ranking Mode: {{ $profile['source_label'] ?? 'Content-Based Profile' }}
                </span>
                <span class="text-xs text-slate-500">
                    Showing top <strong>{{ $recommendations->count() }}</strong> ranked properties
                </span>
            </div>
            <p class="text-xs text-slate-600">
                Each property below is scored out of 100 points and ordered from highest match to lowest, with clear <strong>“Why suggested”</strong> reasons.
            </p>
        </div>

        <!-- Inferred / Selected Signals Pills -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            @if(!empty($profile['locations']))
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium border border-slate-200">
                    <strong>Locations:</strong> {{ implode(', ', array_slice($profile['locations'], 0, 2)) }}
                </span>
            @endif
            @if(!empty($profile['property_types']))
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium border border-slate-200">
                    <strong>Types:</strong>
                    {{ collect($profile['property_types'])->map(fn($t) => \App\Models\Property::PROPERTY_TYPES[$t] ?? ucfirst($t))->implode(', ') }}
                </span>
            @endif
            @if(!empty($profile['bedrooms']))
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium border border-slate-200">
                    <strong>Beds:</strong> ~{{ $profile['bedrooms'] }} Beds
                </span>
            @endif
            @if(!empty($profile['target_price']))
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium border border-slate-200">
                    <strong>Ref. Budget:</strong> ₦{{ number_format((float) $profile['target_price'], 0) }}
                </span>
            @endif
        </div>
    </div>

    <!-- Skeleton Loader for Ranked Recommendation Cards -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.property-card :count="6" />
    </div>

    <!-- Ranked Recommendation Cards -->
    <div data-np-content class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200">
        @forelse($recommendations as $item)
            <x-recommended-property-card
                :item="$item"
                :savedPropertyIds="$savedPropertyIds"
                :showBreakdown="true"
            />
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <h3 class="text-base font-bold text-slate-900">No Available Properties to Recommend Yet</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Once available property listings are published, our AI recommendation engine will rank them here.
                </p>
            </div>
        @endforelse
    </div>
</section>
@endsection
