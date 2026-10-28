@extends('layouts.app')

@section('title', 'My Dashboard – NaijaProps')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Dashboard Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-700 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/15 text-blue-100 border border-white/20">
                        {{ $user->role_label }} Account
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold">Welcome, {{ $user->full_name }}</h1>
                <p class="text-blue-100 text-sm mt-1">
                    @if($user->canListProperties())
                        Manage your Nigerian property listings and respond to client enquiries across Lagos, Abuja &amp; Port Harcourt.
                    @else
                        Track your saved properties, enquiries, and tailored Nigerian property recommendations.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('properties.index') }}" class="bg-blue-800/80 hover:bg-blue-800 text-white border border-blue-500/40 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    Browse Properties
                </a>
                @if($user->canListProperties())
                    <a href="{{ route('properties.my') }}" class="bg-blue-800/80 hover:bg-blue-800 text-white border border-blue-500/40 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                        Manage My Listings
                    </a>
                    <a href="{{ route('properties.create') }}" class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                        + List New Property
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if($user->canListProperties())
        <!-- OWNER / AGENT METRICS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">My Listings</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['total_listings'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Total properties listed</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Available Now</p>
                <p class="text-3xl font-extrabold text-emerald-700 mt-2">{{ $stats['available_listings'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Active on marketplace</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Enquiries</p>
                <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $stats['pending_enquiries'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Awaiting your response</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Saved Listings</p>
                <p class="text-3xl font-extrabold text-blue-800 mt-2">{{ $stats['saved_count'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Bookmarked by you</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- My Properties List -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">My Property Listings</h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('properties.my') }}" class="text-xs font-semibold text-blue-800 hover:underline">
                            Manage All ({{ $stats['total_listings'] }}) &rarr;
                        </a>
                        <a href="{{ route('properties.create') }}" class="text-xs font-bold bg-blue-800 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                            + Add Listing
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($properties as $property)
                        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition">
                            <div class="flex items-start space-x-4">
                                <a href="{{ route('properties.show', $property) }}" class="shrink-0">
                                    <img
                                        src="{{ $property->primary_image_url }}"
                                        alt="{{ $property->title }}"
                                        class="w-20 h-16 rounded-xl object-cover bg-slate-100 border border-slate-200"
                                    >
                                </a>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800">
                                            For {{ ucfirst($property->transaction_type) }}
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold uppercase rounded
                                            @if($property->status === 'available') bg-emerald-100 text-emerald-800
                                            @elseif($property->status === 'pending') bg-amber-100 text-amber-800
                                            @else bg-slate-200 text-slate-700 @endif">
                                            {{ ucfirst($property->status) }}
                                        </span>
                                    </div>
                                    <a href="{{ route('properties.show', $property) }}">
                                        <h3 class="text-sm font-bold text-slate-900 hover:text-blue-800 mt-1">{{ $property->title }}</h3>
                                    </a>
                                    <p class="text-xs text-slate-500">{{ $property->location }} &bull; {{ $property->property_type_label }}</p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right shrink-0 flex sm:flex-col items-center sm:items-end justify-between gap-2">
                                <div>
                                    <p class="text-sm font-extrabold text-blue-900">{{ $property->formatted_price }}</p>
                                    <p class="text-[11px] text-slate-400">Listed {{ $property->created_at?->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('properties.show', $property) }}" class="text-[11px] font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-md">View</a>
                                    <a href="{{ route('properties.edit', $property) }}" class="text-[11px] font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-md">Edit</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">
                            <p>You have not listed any properties yet.</p>
                            <a href="{{ route('properties.create') }}" class="mt-3 inline-block text-xs font-bold bg-blue-800 text-white px-4 py-2 rounded-xl">
                                + Create Your First Property Listing
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Client Enquiries -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Recent Enquiries Received</h2>
                    <a href="{{ route('enquiries.index', ['tab' => 'received']) }}" class="text-xs font-semibold text-blue-800 hover:underline">
                        View All &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($receivedEnquiries as $enquiry)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900">{{ $enquiry->user?->full_name }}</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                                    @if($enquiry->status === 'pending') bg-amber-100 text-amber-800
                                    @elseif($enquiry->status === 'replied') bg-emerald-100 text-emerald-800
                                    @else bg-slate-200 text-slate-700 @endif">
                                    {{ ucfirst($enquiry->status) }}
                                </span>
                            </div>
                            @if($enquiry->property)
                                <a href="{{ route('properties.show', $enquiry->property) }}" class="block text-xs font-medium text-blue-800 hover:underline truncate">
                                    {{ $enquiry->property->title }}
                                </a>
                            @endif
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $enquiry->message }}</p>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] text-slate-400">{{ $enquiry->created_at?->diffForHumans() }}</span>
                                <div class="flex items-center gap-2">
                                    @if($enquiry->status === 'pending')
                                        <form method="POST" action="{{ route('enquiries.update-status', $enquiry) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="replied">
                                            <button type="submit" class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded transition">
                                                Mark Replied
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('enquiries.index', ['tab' => 'received']) }}" class="text-[11px] font-semibold text-blue-800 hover:underline">
                                        Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">
                            No enquiries received on your listings yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <!-- BUYER / TENANT METRICS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <a href="{{ route('saved-properties.index') }}" class="bg-white rounded-2xl border border-slate-200 hover:border-blue-300 p-5 shadow-sm transition block">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Saved Properties</p>
                <p class="text-3xl font-extrabold text-blue-800 mt-2">{{ $stats['saved_count'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Properties bookmarked for comparison &rarr;</p>
            </a>
            <a href="{{ route('enquiries.index') }}" class="bg-white rounded-2xl border border-slate-200 hover:border-blue-300 p-5 shadow-sm transition block">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">My Enquiries</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['enquiries_sent'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Messages sent to owners &amp; agents &rarr;</p>
            </a>
            <a href="{{ route('properties.index') }}" class="bg-white rounded-2xl border border-slate-200 hover:border-blue-300 p-5 shadow-sm transition block">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active Marketplace</p>
                <p class="text-3xl font-extrabold text-emerald-700 mt-2">{{ $stats['available_properties'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Verified properties available now &rarr;</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Saved Properties -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Saved Properties</h2>
                    <a href="{{ route('saved-properties.index') }}" class="text-xs font-semibold text-blue-800 hover:underline">
                        View All ({{ $stats['saved_count'] }}) &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($savedProperties as $saved)
                        @if($saved->property)
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition">
                                <div class="flex items-start space-x-4">
                                    <a href="{{ route('properties.show', $saved->property) }}" class="shrink-0">
                                        <img
                                            src="{{ $saved->property->primary_image_url }}"
                                            alt="{{ $saved->property->title }}"
                                            class="w-20 h-16 rounded-xl object-cover bg-slate-100 border border-slate-200"
                                        >
                                    </a>
                                    <div>
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800">
                                            For {{ ucfirst($saved->property->transaction_type) }}
                                        </span>
                                        <a href="{{ route('properties.show', $saved->property) }}">
                                            <h3 class="text-sm font-bold text-slate-900 hover:text-blue-800 mt-1">{{ $saved->property->title }}</h3>
                                        </a>
                                        <p class="text-xs text-slate-500">{{ $saved->property->location }}</p>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right shrink-0 flex sm:flex-col items-center sm:items-end justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-extrabold text-blue-900">{{ $saved->property->formatted_price }}</p>
                                        <p class="text-[11px] text-slate-400">Saved {{ $saved->created_at?->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('properties.show', $saved->property) }}" class="text-[11px] font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-md">
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('saved-properties.destroy', $saved->property) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[11px] font-semibold text-red-700 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-md">
                                                Unsave
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">
                            <p>You haven't saved any properties yet.</p>
                            <a href="{{ route('properties.index') }}" class="mt-3 inline-block text-xs font-bold bg-blue-800 text-white px-4 py-2 rounded-xl">
                                Browse Properties
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sent Enquiries -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">My Recent Enquiries</h2>
                    <a href="{{ route('enquiries.index') }}" class="text-xs font-semibold text-blue-800 hover:underline">
                        View All ({{ $stats['enquiries_sent'] }}) &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($myEnquiries as $enquiry)
                        <div class="p-4 space-y-1.5">
                            <div class="flex items-center justify-between">
                                @if($enquiry->property)
                                    <a href="{{ route('properties.show', $enquiry->property) }}" class="text-xs font-bold text-blue-900 hover:underline truncate">
                                        {{ $enquiry->property->title }}
                                    </a>
                                @else
                                    <span class="text-xs font-bold text-blue-900 truncate">Property Listing</span>
                                @endif
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                                    @if($enquiry->status === 'pending') bg-amber-100 text-amber-800
                                    @elseif($enquiry->status === 'replied') bg-emerald-100 text-emerald-800
                                    @else bg-slate-200 text-slate-700 @endif">
                                    {{ ucfirst($enquiry->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $enquiry->message }}</p>
                            <p class="text-[11px] text-slate-400">Sent {{ $enquiry->created_at?->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">
                            You haven't sent any property enquiries yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Personalized AI Recommendations for Buyer / Tenant -->
        @if(isset($recommendedProperties) && $recommendedProperties->isNotEmpty())
            <div class="mt-8 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-900 mb-2">
                            <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            AI-Assisted Recommendations
                        </span>
                        <h2 class="text-xl font-extrabold text-slate-900">Tailored Properties Recommended For You</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $recommendationProfile['source_label'] ?? 'Ranked based on your saved properties, enquiries, and preferred locations' }}
                        </p>
                    </div>
                    <a href="{{ route('recommendations.index') }}" class="text-xs font-bold text-blue-800 hover:bg-blue-100 bg-blue-50 border border-blue-200 px-3.5 py-2 rounded-xl transition shrink-0">
                        View All &amp; Customize Preferences &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recommendedProperties as $recItem)
                        <x-recommended-property-card
                            :item="$recItem"
                            :savedPropertyIds="$savedPropertyIds ?? []"
                            :showBreakdown="true"
                        />
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
