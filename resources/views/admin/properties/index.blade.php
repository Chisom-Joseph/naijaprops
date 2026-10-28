@extends('layouts.app')

@section('title', 'Manage All Properties – Admin – NaijaProps')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Admin Header Banner -->
    <div class="bg-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-sm mb-8 border border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-200 border border-purple-400/30">
                        System Administrator
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold">Manage All Property Listings</h1>
                <p class="text-slate-300 text-sm mt-1">
                    Approve, reject/place on hold, edit, or remove any property listing across all owners and agents on NaijaProps.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    &larr; Admin Dashboard
                </a>
                <a href="{{ route('admin.reports.index') }}" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                    Review Reports
                </a>
                <a href="{{ route('properties.create') }}" class="bg-blue-700 hover:bg-blue-600 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                    + New Property Listing
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Properties</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Approved / Available</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1">{{ $stats['available'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending / On Hold</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sold / Rented</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-800 mt-1">{{ $stats['closed'] }}</p>
        </div>
        <a href="{{ route('admin.properties.index', ['flagged' => 1]) }}" class="col-span-2 lg:col-span-1 bg-white hover:bg-red-50/40 rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm transition block">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Flagged / Reported</p>
            <p class="text-2xl sm:text-3xl font-extrabold {{ ($stats['flagged'] ?? 0) > 0 ? 'text-red-600' : 'text-slate-900' }} mt-1">
                {{ $stats['flagged'] ?? 0 }}
            </p>
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.properties.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by title, location, or owner name/email..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-700 focus:outline-none"
                >
            </div>
            <div>
                <select name="property_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none">
                    <option value="">All Property Types</option>
                    @foreach(\App\Models\Property::PROPERTY_TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(request('property_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center">
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer select-none px-2">
                    <input
                        type="checkbox"
                        name="flagged"
                        value="1"
                        @checked(request()->boolean('flagged'))
                        class="rounded border-slate-300 text-red-600 focus:ring-red-500"
                    >
                    <span>Flagged Only</span>
                </label>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" data-loading-text="Filtering..." class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded-xl transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'property_type', 'transaction_type', 'status', 'flagged']))
                    <a href="{{ route('admin.properties.index') }}" data-skeleton-trigger class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Skeleton Loader for Admin Properties Table -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.table-rows :rows="6" :showThumbnail="true" />
    </div>

    <!-- Admin Properties Table -->
    <div data-np-content class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-opacity duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Property</th>
                        <th class="py-3.5 px-4">Owner / Agent</th>
                        <th class="py-3.5 px-4">Price (₦)</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Moderation &amp; Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($properties as $property)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('properties.show', $property) }}" class="shrink-0">
                                        <img
                                            src="{{ $property->primary_image_url }}"
                                            alt="{{ $property->title }}"
                                            class="w-16 h-12 rounded-lg object-cover border border-slate-200 bg-slate-100"
                                        >
                                    </a>
                                    <div class="min-w-0">
                                        <a href="{{ route('properties.show', $property) }}" class="font-bold text-slate-900 hover:text-blue-800 line-clamp-1">
                                            {{ $property->title }}
                                        </a>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $property->location }} &bull; {{ $property->property_type_label }} ({{ ucfirst($property->transaction_type) }})
                                        </p>
                                        <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                                            <span>{{ $property->images_count }} photos</span>
                                            <span>&bull;</span>
                                            <span>{{ $property->enquiries_count }} enquiries</span>
                                            @if($property->reports_count > 0)
                                                <span>&bull;</span>
                                                <a
                                                    href="{{ route('admin.reports.index', ['property_id' => $property->id]) }}"
                                                    class="text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-2 py-0.2 rounded-full font-bold transition"
                                                >
                                                    {{ $property->reports_count }} {{ \Illuminate\Support\Str::plural('report', $property->reports_count) }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4">
                                <p class="font-semibold text-slate-800 text-xs">{{ $property->owner?->full_name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $property->owner?->role_label }}</p>
                            </td>

                            <td class="py-4 px-4">
                                <p class="font-extrabold text-blue-900">{{ $property->formatted_price }}</p>
                                <p class="text-[11px] text-slate-400">For {{ ucfirst($property->transaction_type) }}</p>
                            </td>

                            <td class="py-4 px-4">
                                <form method="POST" action="{{ route('properties.update-status', $property) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        aria-label="Update status"
                                        class="text-xs font-semibold rounded-lg border px-2.5 py-1 focus:outline-none
                                            @if($property->status === 'available') bg-emerald-50 border-emerald-200 text-emerald-800
                                            @elseif($property->status === 'pending') bg-amber-50 border-amber-200 text-amber-800
                                            @else bg-slate-100 border-slate-300 text-slate-700 @endif"
                                    >
                                        @foreach(\App\Models\Property::STATUSES as $statusKey => $statusLabel)
                                            <option value="{{ $statusKey }}" @selected($property->status === $statusKey)>
                                                {{ $statusLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>

                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex flex-wrap items-center justify-end gap-1.5">
                                    @if($property->status !== 'available')
                                        <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1.5 rounded-lg transition">
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    @if($property->status === 'available')
                                        <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2.5 py-1.5 rounded-lg transition">
                                                Reject / Hold
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('properties.show', $property) }}" class="text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1.5 rounded-lg transition">
                                        View
                                    </a>
                                    <a href="{{ route('properties.edit', $property) }}" class="text-xs font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1.5 rounded-lg transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Delete this property listing permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-2.5 py-1.5 rounded-lg transition">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-sm text-slate-500">
                                No properties found matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($properties->hasPages())
        <div class="mt-6">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection
