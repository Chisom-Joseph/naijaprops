@extends('layouts.app')

@section('title', 'Manage Users – NaijaProps Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <nav class="text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-800">Admin Dashboard</a>
                <span class="mx-1">/</span>
                <span class="text-slate-800 font-semibold">User Management</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900">User Management &amp; Role Control</h1>
            <p class="text-sm text-slate-600">Activate/deactivate accounts and assign roles (Admin, Owner, Agent, Buyer/Tenant).</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 px-4 py-2 rounded-xl shadow-sm transition">
            &larr; Back to Admin Overview
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label for="search" class="sr-only">Search users</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by full name, email, or phone..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none"
                >
            </div>
            <div>
                <label for="role" class="sr-only">Filter by Role</label>
                <select
                    id="role"
                    name="role"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none"
                >
                    <option value="">All Roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                    <option value="owner" @selected(request('role') === 'owner')>Property Owner</option>
                    <option value="agent" @selected(request('role') === 'agent')>Real Estate Agent</option>
                    <option value="buyer" @selected(request('role') === 'buyer')>Buyer / Tenant</option>
                </select>
            </div>
            <div class="flex gap-2">
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm bg-white focus:border-blue-700 focus:outline-none"
                >
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
                <button type="submit" data-loading-text="Filtering..." class="bg-blue-800 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Skeleton Loader for Users Table -->
    <div data-np-skeleton class="hidden mb-6">
        <x-skeletons.table-rows :rows="6" :showThumbnail="false" />
    </div>

    <!-- Users Table -->
    <div data-np-content class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-opacity duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-5">User Details</th>
                        <th class="py-3.5 px-4">Activity</th>
                        <th class="py-3.5 px-4">Role &amp; Account Status</th>
                        <th class="py-3.5 px-5 text-right">Quick Toggle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-800 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">
                                            {{ $u->full_name }}
                                            @if(auth()->id() === $u->id)
                                                <span class="ml-1 text-[10px] font-semibold bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full">You</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $u->email }} {{ $u->phone ? '• ' . $u->phone : '' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4 text-xs text-slate-600">
                                <span class="inline-block mr-2"><strong>{{ $u->properties_count }}</strong> Listings</span>
                                <span class="inline-block"><strong>{{ $u->enquiries_count }}</strong> Enquiries</span>
                            </td>

                            <td class="py-4 px-4">
                                @if(auth()->id() === $u->id)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-100 text-purple-800">
                                            Administrator
                                        </span>
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Active
                                        </span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="role"
                                            class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium bg-white focus:border-blue-700 focus:outline-none"
                                        >
                                            <option value="buyer" @selected($u->role === 'buyer')>Buyer / Tenant</option>
                                            <option value="owner" @selected($u->role === 'owner')>Property Owner</option>
                                            <option value="agent" @selected($u->role === 'agent')>Real Estate Agent</option>
                                            <option value="admin" @selected($u->role === 'admin')>Administrator</option>
                                        </select>

                                        <select
                                            name="status"
                                            class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium bg-white focus:border-blue-700 focus:outline-none"
                                        >
                                            <option value="active" @selected($u->status === 'active')>Active</option>
                                            <option value="inactive" @selected($u->status === 'inactive')>Inactive</option>
                                        </select>

                                        <button type="submit" class="bg-slate-900 hover:bg-blue-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                            Save
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right">
                                @if(auth()->id() !== $u->id)
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $u) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition
                                                {{ $u->status === 'active'
                                                    ? 'border-red-200 text-red-700 hover:bg-red-50'
                                                    : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}"
                                        >
                                            {{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500 text-sm">
                                No users matched your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
