@extends('layouts.app')

@section('title', 'Property Enquiries – NaijaProps')

@section('content')
@php($user = auth()->user())
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-700 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/15 text-blue-100 border border-white/20">
                    Direct Property Communication
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold mt-2">
                    @if($user->canListProperties() && $tab === 'received')
                        Property Enquiries Inbox
                    @else
                        My Sent Property Enquiries
                    @endif
                </h1>
                <p class="text-blue-100 text-sm mt-1">
                    @if($user->canListProperties() && $tab === 'received')
                        Review and manage enquiries from prospective buyers and tenants interested in your listings.
                    @else
                        Track messages you have sent to property owners and real estate agents across Nigeria.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" class="bg-blue-800/80 hover:bg-blue-800 text-white border border-blue-500/40 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    My Dashboard
                </a>
                <a href="{{ route('properties.index') }}" class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                    Browse Properties
                </a>
            </div>
        </div>
    </div>

    <!-- Tab Switcher for Owners / Agents / Admins -->
    @if($user->canListProperties())
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <a
                href="{{ route('enquiries.index', ['tab' => 'received']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition {{ $tab === 'received' ? 'bg-blue-800 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
            >
                <span>Received on Listings</span>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $tab === 'received' ? 'bg-blue-900 text-blue-100' : 'bg-slate-100 text-slate-700' }}">
                    {{ $stats['received_count'] }}
                </span>
            </a>

            <a
                href="{{ route('enquiries.index', ['tab' => 'sent']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition {{ $tab === 'sent' ? 'bg-blue-800 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
            >
                <span>Sent by Me</span>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $tab === 'sent' ? 'bg-blue-900 text-blue-100' : 'bg-slate-100 text-slate-700' }}">
                    {{ $stats['sent_count'] }}
                </span>
            </a>
        </div>
    @endif

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Enquiries</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Response</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Replied</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1">{{ $stats['replied'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Closed</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-800 mt-1">{{ $stats['closed'] }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('enquiries.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <div class="{{ $tab === 'received' && $myProperties->isNotEmpty() ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by message, property title, or sender..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-700 focus:outline-none"
                >
            </div>

            @if($tab === 'received' && $myProperties->isNotEmpty())
                <div class="lg:col-span-4">
                    <select name="property_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none">
                        <option value="">All My Properties</option>
                        @foreach($myProperties as $propOption)
                            <option value="{{ $propOption->id }}" @selected((string) request('property_id') === (string) $propOption->id)>
                                {{ \Illuminate\Support\Str::limit($propOption->title, 45) }} ({{ $propOption->location }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="lg:col-span-2">
                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Enquiry::STATUSES as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" data-loading-text="Filtering..." class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded-xl transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'property_id']))
                    <a href="{{ route('enquiries.index', ['tab' => $tab]) }}" data-skeleton-trigger class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Skeleton Loader for Enquiries List -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.list-items :items="3" variant="enquiry" />
    </div>

    <!-- Enquiries List -->
    <div data-np-content class="space-y-4 transition-opacity duration-200">
        @forelse($enquiries as $enquiry)
            @php($property = $enquiry->property)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm hover:border-blue-200 transition">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <!-- Left: Sender/Recipient Details & Enquiry Message -->
                    <div class="flex-1 space-y-4">
                        <!-- Top Meta Row -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center space-x-3">
                                @if($tab === 'received')
                                    <div class="w-10 h-10 rounded-xl bg-blue-900 text-white font-bold text-sm flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($enquiry->user?->full_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h2 class="text-sm font-bold text-slate-900">{{ $enquiry->user?->full_name ?? 'Unknown User' }}</h2>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-100 text-slate-700">
                                                {{ $enquiry->user?->role_label ?? 'Buyer / Tenant' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            {{ $enquiry->user?->email }}
                                            @if($enquiry->user?->phone)
                                                &bull; <span class="font-semibold text-blue-800">{{ $enquiry->user->phone }}</span>
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-800 border border-blue-200 font-bold text-sm flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($property?->owner?->full_name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h2 class="text-sm font-bold text-slate-900">
                                                To: {{ $property?->owner?->full_name ?? 'Property Provider' }}
                                            </h2>
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-blue-50 text-blue-800">
                                                {{ $property?->owner?->role_label ?? 'Owner / Agent' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            {{ $property?->owner?->email }}
                                            @if($property?->owner?->phone)
                                                &bull; <span class="font-semibold text-blue-800">{{ $property->owner->phone }}</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Badge & Timestamp -->
                            <div class="flex items-center gap-2.5">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    @if($enquiry->status === 'pending') bg-amber-100 text-amber-800
                                    @elseif($enquiry->status === 'replied') bg-emerald-100 text-emerald-800
                                    @else bg-slate-200 text-slate-700 @endif">
                                    {{ $enquiry->status_label }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    {{ $enquiry->created_at?->format('M d, Y \a\t g:i A') }} ({{ $enquiry->created_at?->diffForHumans() }})
                                </span>
                            </div>
                        </div>

                        <!-- Enquiry Message Bubble -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Enquiry Message</p>
                            <p class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $enquiry->message }}</p>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($tab === 'received')
                                    @if($enquiry->user?->phone)
                                        <a
                                            href="tel:{{ preg_replace('/\s+/', '', $enquiry->user->phone) }}"
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1.5 rounded-lg transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            Call {{ $enquiry->user->phone }}
                                        </a>
                                    @endif

                                    @if($enquiry->user?->email)
                                        <a
                                            href="mailto:{{ $enquiry->user->email }}?subject={{ rawurlencode('Re: Your NaijaProps Enquiry on ' . ($property?->title ?? 'Property')) }}"
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            Email Reply
                                        </a>
                                    @endif
                                @else
                                    @if($property?->owner?->phone)
                                        <a
                                            href="tel:{{ preg_replace('/\s+/', '', $property->owner->phone) }}"
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1.5 rounded-lg transition"
                                        >
                                            Call {{ $property->owner->phone }}
                                        </a>
                                    @endif
                                @endif
                            </div>

                            <!-- Status Controls & Delete -->
                            <div class="flex flex-wrap items-center gap-2">
                                @can('update', $enquiry)
                                    @if($enquiry->status === 'pending')
                                        <form method="POST" action="{{ route('enquiries.update-status', $enquiry) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="replied">
                                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg shadow-sm transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Mark as Replied
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('enquiries.update-status', $enquiry) }}" class="inline-flex items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="status"
                                            onchange="this.form.submit()"
                                            aria-label="Update enquiry status"
                                            class="text-xs font-semibold rounded-lg border border-slate-300 px-2.5 py-1.5 bg-white text-slate-700 focus:border-blue-600 focus:outline-none"
                                        >
                                            @foreach(\App\Models\Enquiry::STATUSES as $statusKey => $statusLabel)
                                                <option value="{{ $statusKey }}" @selected($enquiry->status === $statusKey)>
                                                    Status: {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endcan

                                @can('delete', $enquiry)
                                    <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-lg transition">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- Right: Referenced Property Card Summary -->
                    @if($property)
                        <div class="lg:w-80 shrink-0 bg-slate-50 rounded-xl p-3.5 border border-slate-200/80 flex items-start space-x-3.5">
                            <a href="{{ route('properties.show', $property) }}" class="shrink-0">
                                <img
                                    src="{{ $property->primary_image_url }}"
                                    alt="{{ $property->title }}"
                                    class="w-20 h-20 rounded-xl object-cover bg-slate-200 border border-slate-200"
                                >
                            </a>
                            <div class="min-w-0 flex-1">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded
                                    {{ $property->transaction_type === 'sale' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    For {{ ucfirst($property->transaction_type) }}
                                </span>
                                <a href="{{ route('properties.show', $property) }}" class="block mt-1">
                                    <h3 class="text-xs font-bold text-slate-900 hover:text-blue-800 line-clamp-2">{{ $property->title }}</h3>
                                </a>
                                <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $property->location }}</p>
                                <p class="text-sm font-extrabold text-blue-900 mt-1">{{ $property->formatted_price }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">
                    @if($tab === 'received')
                        No Enquiries Received Yet
                    @else
                        No Enquiries Sent Yet
                    @endif
                </h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    @if($tab === 'received')
                        When prospective buyers or tenants send an enquiry on your property listings, their messages will appear here.
                    @else
                        Have a question about a property's title documents, service charge, or inspection schedule? Open any listing to message the owner or agent directly.
                    @endif
                </p>
                <a href="{{ route('properties.index') }}" class="mt-5 inline-flex items-center gap-1.5 bg-blue-800 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                    Browse Properties
                </a>
            </div>
        @endforelse
    </div>

    @if($enquiries->hasPages())
        <div class="mt-8">
            {{ $enquiries->links() }}
        </div>
    @endif
</div>
@endsection
