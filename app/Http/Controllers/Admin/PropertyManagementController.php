<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PropertyManagementController extends Controller
{
    /**
     * Display a filterable, paginated list of all properties for administrators.
     */
    public function index(Request $request): View
    {
        $query = Property::query()
            ->with(['primaryImage', 'owner'])
            ->withCount(['images', 'enquiries', 'reports']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                        $ownerQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('property_type') && array_key_exists($request->input('property_type'), Property::PROPERTY_TYPES)) {
            $query->where('property_type', $request->input('property_type'));
        }

        if ($request->filled('transaction_type') && in_array($request->input('transaction_type'), ['sale', 'rent'], true)) {
            $query->where('transaction_type', $request->input('transaction_type'));
        }

        if ($request->filled('status') && array_key_exists($request->input('status'), Property::STATUSES)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->boolean('flagged')) {
            $query->has('reports');
        }

        $properties = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => Property::count(),
            'available' => Property::where('status', 'available')->count(),
            'pending' => Property::where('status', 'pending')->count(),
            'closed' => Property::whereIn('status', ['sold', 'rented'])->count(),
            'flagged' => Property::has('reports')->count(),
        ];

        return view('admin.properties.index', compact('properties', 'stats'));
    }

    /**
     * Approve a property listing (sets status to 'available').
     */
    public function approve(Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);

        $property->update(['status' => 'available']);

        return back()->with('success', "Listing \"{$property->title}\" has been approved and is now publicly available.");
    }

    /**
     * Reject / place a property listing on hold (sets status to 'pending').
     */
    public function reject(Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);

        $property->update(['status' => 'pending']);

        return back()->with('success', "Listing \"{$property->title}\" has been rejected/placed on hold (Pending) and removed from public available listings.");
    }
}
