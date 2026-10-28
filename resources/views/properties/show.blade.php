@extends('layouts.app')

@section('title', $property->title . ' – NaijaProps')

@section('content')
@php
    $galleryUrls = $property->images->map(fn($img) => $img->url)->values()->all();
    if (empty($galleryUrls)) {
        $galleryUrls = [$property->primary_image_url];
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Top Breadcrumb & Owner/Admin Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <nav class="flex items-center space-x-2 text-xs font-medium text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-blue-800 transition">Home</a>
            <span>/</span>
            <a href="{{ route('properties.index') }}" class="hover:text-blue-800 transition">Properties</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-[220px] sm:max-w-md">{{ $property->title }}</span>
        </nav>

        @auth
            @if(auth()->user()->can('update', $property) || auth()->user()->can('delete', $property))
                <div class="flex flex-wrap items-center gap-2 bg-white border border-slate-200 px-3 py-2 rounded-xl shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">
                        {{ auth()->user()->isAdmin() && auth()->id() !== $property->owner_id ? 'Admin Control:' : 'Manage Listing:' }}
                    </span>

                    @if(auth()->user()->isAdmin())
                        @if($property->status !== 'available')
                            <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 px-3 py-1.5 rounded-lg transition">
                                    Approve Listing
                                </button>
                            </form>
                        @endif

                        @if($property->status !== 'pending')
                            <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-lg transition">
                                    Reject / Hold
                                </button>
                            </form>
                        @endif
                    @endif

                    @can('update', $property)
                        <!-- Quick Status Switcher -->
                        <form method="POST" action="{{ route('properties.update-status', $property) }}" class="inline-flex items-center">
                            @csrf
                            @method('PATCH')
                            <select
                                name="status"
                                onchange="this.form.submit()"
                                aria-label="Change property status"
                                class="text-xs font-semibold rounded-lg border border-slate-300 px-2.5 py-1.5 bg-slate-50 text-slate-800 focus:border-blue-600 focus:outline-none"
                            >
                                @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                                    <option value="{{ $statusKey }}" @selected($property->status === $statusKey)>
                                        Status: {{ $statusLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <a href="{{ route('properties.edit', $property) }}" class="inline-flex items-center gap-1 text-xs font-semibold bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Listing
                        </a>
                    @endcan

                    @can('delete', $property)
                        <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Are you sure you want to permanently delete this property listing and all its uploaded photos?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            @endif
        @endauth
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Columns: Gallery, Header, Specs & Description -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Interactive Multi-Image Gallery -->
            <div
                data-animate="fade-up"
                x-data="{
                    images: @js($galleryUrls),
                    activeIndex: 0,
                    prev() { this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length },
                    next() { this.activeIndex = (this.activeIndex + 1) % this.images.length }
                }"
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
            >
                <div class="relative h-72 sm:h-[420px] bg-slate-900">
                    <img
                        :src="images[activeIndex]"
                        alt="{{ $property->title }}"
                        class="w-full h-full object-cover"
                    >

                    <!-- Top Left Badges -->
                    <div class="absolute top-4 left-4 flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow
                            {{ $property->transaction_type === 'sale' ? 'bg-blue-800 text-white' : 'bg-emerald-700 text-white' }}">
                            For {{ ucfirst($property->transaction_type) }}
                        </span>
                        <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-white/95 text-slate-900 shadow">
                            {{ $property->property_type_label }}
                        </span>
                    </div>

                    <!-- Top Right Status Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow
                            @if($property->status === 'available') bg-emerald-500 text-white
                            @elseif($property->status === 'pending') bg-amber-500 text-white
                            @else bg-slate-800 text-white @endif">
                            {{ \App\Models\Property::STATUSES[$property->status] ?? ucfirst($property->status) }}
                        </span>
                    </div>

                    <!-- Prev / Next Arrows if multiple images -->
                    <template x-if="images.length > 1">
                        <div>
                            <button
                                type="button"
                                @click="prev()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/70 hover:bg-slate-900 text-white flex items-center justify-center shadow transition"
                                aria-label="Previous image"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click="next()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/70 hover:bg-slate-900 text-white flex items-center justify-center shadow transition"
                                aria-label="Next image"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <div class="absolute bottom-3 right-4 bg-slate-900/75 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                <span x-text="activeIndex + 1"></span> / <span x-text="images.length"></span> Photos
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Thumbnail Strip -->
                @if(count($galleryUrls) > 1)
                    <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center gap-2.5 overflow-x-auto">
                        @foreach($galleryUrls as $idx => $imgUrl)
                            <button
                                type="button"
                                @click="activeIndex = {{ $idx }}"
                                :class="activeIndex === {{ $idx }} ? 'ring-2 ring-blue-700 opacity-100' : 'opacity-65 hover:opacity-100'"
                                class="w-20 h-14 rounded-xl overflow-hidden shrink-0 border border-slate-300 transition focus:outline-none"
                            >
                                <img src="{{ $imgUrl }}" alt="Property thumbnail {{ $idx + 1 }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Title, Price & Key Attributes Card -->
            <div data-animate="fade-up" data-animate-delay="60" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                            {{ $property->title }}
                        </h1>
                        <p class="text-sm text-slate-600 mt-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $property->location }}</span>
                        </p>

                        <!-- Smooth-Scroll Quick Action Jump Pills -->
                        <div class="mt-3.5 flex flex-wrap items-center gap-2 text-xs font-semibold">
                            <a href="#enquiry-section" class="np-btn-interactive inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <span>Send Enquiry</span>
                            </a>
                            @if(isset($similarRecommendations) && $similarRecommendations->isNotEmpty())
                                <a href="#similar-properties" class="np-btn-interactive inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1 rounded-lg transition">
                                    <span>Similar AI Picks</span>
                                    <span>&darr;</span>
                                </a>
                            @endif
                            <a href="#report-section" class="np-btn-interactive inline-flex items-center gap-1 text-slate-500 hover:text-red-700 px-2 py-1 rounded-lg transition">
                                <span>Report Listing</span>
                            </a>
                        </div>
                    </div>

                    <div class="sm:text-right shrink-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            {{ $property->transaction_type === 'rent' ? 'Annual Rent' : 'Asking Price' }}
                        </p>
                        <p class="text-2xl sm:text-3xl font-extrabold text-blue-900 mt-0.5">
                            {{ $property->formatted_price }}
                            @if($property->transaction_type === 'rent')
                                <span class="text-xs font-normal text-slate-500">/ yr</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Key Features Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-100">
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Property Type</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $property->property_type_label }}</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Bedrooms</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">
                            {{ !is_null($property->bedrooms) ? $property->bedrooms . ' Bedrooms' : 'N/A (Land/Plot)' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Bathrooms</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">
                            {{ !is_null($property->bathrooms) ? $property->bathrooms . ' Bathrooms' : 'N/A (Land/Plot)' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Market Status</p>
                        <p class="text-sm font-bold mt-1
                            @if($property->status === 'available') text-emerald-700
                            @elseif($property->status === 'pending') text-amber-700
                            @else text-slate-700 @endif">
                            {{ \App\Models\Property::STATUSES[$property->status] ?? ucfirst($property->status) }}
                        </p>
                    </div>
                </div>

                <!-- Property Description -->
                <div class="pt-6">
                    <h2 class="text-base font-bold text-slate-900 mb-3">Property Description</h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $property->description }}</div>
                </div>

                <!-- Metadata Footer -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400">
                    <span>Listing Reference: <strong class="text-slate-600">#NP-{{ str_pad((string) $property->id, 4, '0', STR_PAD_LEFT) }}</strong></span>
                    <span>Listed {{ $property->created_at?->format('M d, Y') }} ({{ $property->created_at?->diffForHumans() }})</span>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Save Action, Listed By Owner/Agent, Send Enquiry Form & Safety Tips -->
        <div class="space-y-6">
            <!-- Listed By & Save Property Card -->
            <div data-animate="fade-up" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Listed By</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                        {{ $property->owner?->role_label ?? 'Property Provider' }}
                    </span>
                </div>

                <div class="flex items-center space-x-3.5 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-900 text-white font-bold text-lg flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($property->owner?->full_name ?? 'N', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-base font-bold text-slate-900 truncate">{{ $property->owner?->full_name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $property->owner?->email }}</p>
                        @if($property->owner?->phone)
                            <p class="text-xs font-semibold text-blue-800 mt-0.5">{{ $property->owner->phone }}</p>
                        @endif
                    </div>
                </div>

                @if($property->owner?->phone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $property->owner->phone) }}" class="w-full mb-2.5 inline-flex items-center justify-center gap-2 bg-blue-800 hover:bg-blue-700 text-white font-semibold text-sm py-2.5 px-4 rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Call {{ $property->owner->phone }}
                    </a>
                @endif

                <!-- Save / Bookmark Property Button -->
                @auth
                    <form method="POST" action="{{ route('saved-properties.toggle', $property) }}" class="mb-4">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 font-semibold text-sm py-2.5 px-4 rounded-xl border transition
                                {{ $isSaved
                                    ? 'bg-red-50 hover:bg-red-100 text-red-700 border-red-200'
                                    : 'bg-slate-50 hover:bg-blue-50 text-slate-800 hover:text-blue-800 border-slate-200 hover:border-blue-200' }}"
                        >
                            @if($isSaved)
                                <svg class="w-4 h-4 fill-current text-red-600" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                <span>Saved in Bookmarks (Unsave)</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>Save Property</span>
                            @endif
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="w-full mb-4 inline-flex items-center justify-center gap-2 bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-800 border border-slate-200 font-semibold text-sm py-2.5 px-4 rounded-xl transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>Sign In to Save Property</span>
                    </a>
                @endauth

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Enquiries Received:</span>
                        <span class="font-bold text-slate-800">{{ $property->enquiries_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Saved by Buyers:</span>
                        <span class="font-bold text-slate-800">{{ $property->saved_properties_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Uploaded Photos:</span>
                        <span class="font-bold text-slate-800">{{ $property->images->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Enquiry System Card -->
            <div id="enquiry-section" data-animate="fade-up" data-animate-delay="60" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-slate-900">Send an Enquiry</h2>
                    <span class="text-[11px] font-semibold text-blue-800 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                        Direct Message
                    </span>
                </div>

                @auth
                    @if(auth()->id() === $property->owner_id)
                        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 space-y-2">
                            <p class="font-bold">This is your property listing.</p>
                            <p class="text-blue-800">
                                You have received <strong>{{ $property->enquiries_count }}</strong> {{ \Illuminate\Support\Str::plural('enquiry', $property->enquiries_count) }} on this property.
                            </p>
                            <a
                                href="{{ route('enquiries.index', ['tab' => 'received', 'property_id' => $property->id]) }}"
                                class="inline-flex items-center gap-1 font-bold text-blue-800 hover:underline pt-1"
                            >
                                View &amp; Manage Enquiries &rarr;
                            </a>
                        </div>
                    @else
                        <p class="text-xs text-slate-500 mb-4">
                            Ask {{ $property->owner?->full_name ?? 'the property owner' }} about inspection dates, title documents, or price negotiation.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('enquiries.store', $property) }}"
                            data-loading-overlay="true"
                            data-loading-title="Sending Property Enquiry..."
                            data-loading-subtitle="Delivering your message directly to the property provider..."
                            class="space-y-3.5"
                        >
                            @csrf
                            <div>
                                <label for="enquiry_message" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Your Message
                                </label>
                                <textarea
                                    id="enquiry_message"
                                    name="message"
                                    rows="4"
                                    required
                                    minlength="10"
                                    maxlength="2000"
                                    placeholder="Good day, I am interested in this property ({{ $property->title }}). Is it still available for inspection this week?"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                data-loading-text="Sending enquiry..."
                                class="w-full bg-blue-800 hover:bg-blue-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                Send Enquiry Message
                            </button>
                        </form>

                        @if(isset($userEnquiries) && $userEnquiries->isNotEmpty())
                            <div class="mt-5 pt-4 border-t border-slate-100 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Your Recent Enquiries ({{ $userEnquiries->count() }})
                                    </p>
                                    <a href="{{ route('enquiries.index', ['tab' => 'sent']) }}" class="text-[11px] font-semibold text-blue-800 hover:underline">
                                        View All &rarr;
                                    </a>
                                </div>
                                @foreach($userEnquiries->take(2) as $pastEnquiry)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] text-slate-400">{{ $pastEnquiry->created_at?->diffForHumans() }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                                @if($pastEnquiry->status === 'pending') bg-amber-100 text-amber-800
                                                @elseif($pastEnquiry->status === 'replied') bg-emerald-100 text-emerald-800
                                                @else bg-slate-200 text-slate-700 @endif">
                                                {{ $pastEnquiry->status_label }}
                                            </span>
                                        </div>
                                        <p class="text-slate-700 line-clamp-2">{{ $pastEnquiry->message }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                @else
                    <p class="text-xs text-slate-600 mb-4">
                        Sign in to your NaijaProps account to send a direct enquiry message to <strong>{{ $property->owner?->full_name }}</strong> and track replies.
                    </p>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('login') }}" class="w-full text-center bg-blue-800 hover:bg-blue-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-sm transition">
                            Sign In to Send Enquiry
                        </a>
                        <a href="{{ route('register') }}" class="w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs py-2 px-4 rounded-xl transition">
                            Create Free Buyer/Tenant Account
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Nigerian Buyer Safety Tips -->
            <div class="bg-blue-950 text-blue-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    NaijaProps Safety Tips
                </h3>
                <ul class="text-xs text-blue-200 space-y-2 list-disc list-inside">
                    <li>Always inspect the physical property in daylight before making any payment.</li>
                    <li>Do not pay upfront inspection fees before meeting the verified owner or agent.</li>
                    <li>Verify title documents (C of O, Governor's Consent, or R of O) for property sales.</li>
                </ul>
            </div>

            <!-- Suspicious Listing Reporting Card -->
            <div
                id="report-section"
                x-data="{
                    openReportForm: {{ $errors->has('reason') ? 'true' : 'false' }},
                    reasonText: @js(old('reason', ''))
                }"
                class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-sm font-bold text-slate-900">Report Suspicious Listing</h3>
                    </div>

                    @if(auth()->check() && auth()->user()->isAdmin() && $property->reports_count > 0)
                        <a
                            href="{{ route('admin.reports.index', ['property_id' => $property->id]) }}"
                            class="text-[11px] font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-2.5 py-0.5 rounded-full transition"
                        >
                            {{ $property->reports_count }} {{ \Illuminate\Support\Str::plural('Report', $property->reports_count) }} &rarr;
                        </a>
                    @endif
                </div>

                @auth
                    @if(auth()->id() === $property->owner_id)
                        <p class="text-xs text-slate-500 mt-2">
                            This is your own property listing. Administrators review any user reports submitted against listings.
                        </p>
                    @else
                        <p class="text-xs text-slate-500 mt-2">
                            Notice misleading information, upfront fee demands, or suspected fraud? Report this listing to NaijaProps Administrators.
                        </p>

                        @if(isset($userReports) && $userReports->isNotEmpty())
                            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-amber-900">Your Submitted Report</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        @if($userReports->first()->status === 'pending') bg-amber-200 text-amber-900
                                        @elseif($userReports->first()->status === 'reviewed') bg-blue-100 text-blue-800
                                        @else bg-emerald-100 text-emerald-800 @endif">
                                        {{ $userReports->first()->status_label }}
                                    </span>
                                </div>
                                <p class="text-amber-800 line-clamp-2">{{ $userReports->first()->reason }}</p>
                                <p class="text-[11px] text-amber-700/80">Submitted {{ $userReports->first()->created_at?->diffForHumans() }}</p>
                            </div>
                        @endif

                        <div class="mt-3">
                            <button
                                type="button"
                                @click="openReportForm = !openReportForm"
                                class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 py-2.5 px-4 rounded-xl transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                </svg>
                                <span x-text="openReportForm ? 'Cancel Report' : '{{ isset($userReports) && $userReports->isNotEmpty() ? 'Update or Submit Another Report' : 'Flag / Report This Listing' }}'"></span>
                            </button>
                        </div>

                        <form
                            x-show="openReportForm"
                            method="POST"
                            action="{{ route('reports.store', $property) }}"
                            class="mt-4 pt-4 border-t border-slate-100 space-y-3"
                        >
                            @csrf
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    Quick-Select Common Issue:
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(\App\Models\Report::REASON_TEMPLATES as $template)
                                        <button
                                            type="button"
                                            @click="reasonText = @js($template)"
                                            class="text-[11px] text-left bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-800 border border-slate-200 hover:border-red-200 px-2.5 py-1 rounded-lg transition"
                                        >
                                            {{ $template }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="report_reason" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                    Reason for Reporting <span class="text-red-600">*</span>
                                </label>
                                <textarea
                                    id="report_reason"
                                    name="reason"
                                    x-model="reasonText"
                                    rows="3"
                                    required
                                    minlength="10"
                                    maxlength="1500"
                                    placeholder="Describe why this listing appears suspicious or inaccurate (at least 10 characters)..."
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 focus:outline-none"
                                >{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                data-loading-text="Submitting report..."
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-sm transition"
                            >
                                Submit Report to Admin
                            </button>
                        </form>
                    @endif
                @else
                    <p class="text-xs text-slate-500 mt-2 mb-3">
                        Spot a suspicious listing or unfair fee request? Sign in to report this property to NaijaProps Administrators.
                    </p>
                    <a
                        href="{{ route('login') }}"
                        class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 py-2 px-4 rounded-xl transition"
                    >
                        Sign In to Report Listing
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- AI Content-Based Similar Properties Section -->
    @if(isset($similarRecommendations) && $similarRecommendations->isNotEmpty())
        <div id="similar-properties" data-animate="fade-up" class="mt-12 pt-8 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-3">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-900 mb-1.5">
                        AI Content-Based Similarity Ranking
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Similar Properties Recommended For You</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ranked by location, property type, Naira (₦) price bracket, and bedrooms similarity to this listing
                    </p>
                </div>
                <a href="{{ route('recommendations.index', ['location' => $property->location, 'property_type' => $property->property_type]) }}" class="text-xs font-bold text-blue-800 hover:underline">
                    Explore More AI Recommendations &rarr;
                </a>
            </div>

            <!-- Skeleton Loader for Similar Properties -->
            <div data-np-skeleton class="hidden mb-6">
                <x-skeletons.property-card :count="3" />
            </div>

            <div data-np-content class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200">
                @foreach($similarRecommendations as $simItem)
                    <x-recommended-property-card
                        :item="$simItem"
                        :savedPropertyIds="$savedPropertyIds ?? []"
                        :showBreakdown="true"
                    />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
