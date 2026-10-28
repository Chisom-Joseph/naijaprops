<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Enquiry;
use App\Models\Property;
use App\Models\Report;
use App\Models\SavedProperty;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display the Admin Dashboard overview with system metrics, reports, and recent activity.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'owners_and_agents' => User::whereIn('role', ['owner', 'agent'])->count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'total_properties' => Property::count(),
            'available_properties' => Property::where('status', 'available')->count(),
            'pending_properties' => Property::where('status', 'pending')->count(),
            'closed_properties' => Property::whereIn('status', ['sold', 'rented'])->count(),
            'sale_properties' => Property::where('transaction_type', 'sale')->count(),
            'rent_properties' => Property::where('transaction_type', 'rent')->count(),
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'reviewed_reports' => Report::where('status', 'reviewed')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
            'total_enquiries' => Enquiry::count(),
            'pending_enquiries' => Enquiry::where('status', 'pending')->count(),
            'total_saved' => SavedProperty::count(),
            'lagos_properties' => Property::where('location', 'like', '%Lagos%')->count(),
            'abuja_properties' => Property::where('location', 'like', '%Abuja%')->count(),
            'ph_properties' => Property::where('location', 'like', '%Port Harcourt%')->count(),
        ];

        $recentUsers = User::latest()->take(6)->get();
        $recentProperties = Property::with('owner')
            ->withCount('reports')
            ->latest()
            ->take(5)
            ->get();
        $pendingReports = Report::with(['property.owner', 'reporter'])
            ->whereIn('status', ['pending', 'reviewed'])
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentProperties', 'pendingReports'));
    }

    /**
     * Display a filterable, paginated list of all users for administrators.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $query = User::query()->withCount(['properties', 'enquiries', 'reports']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->input('role'), ['admin', 'owner', 'agent', 'buyer'], true)) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'], true)) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Update a user's role and/or status.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validated();

        // Prevent the currently logged-in administrator from demoting or deactivating themselves
        if ($request->user()->id === $user->id) {
            if ($validated['role'] !== 'admin' || $validated['status'] !== 'active') {
                return back()->withErrors([
                    'user' => 'You cannot demote or deactivate your own administrator account.',
                ]);
            }
        }

        $user->update([
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Updated {$user->full_name}'s account ({$user->role_label}, {$user->status}).");
    }

    /**
     * Quick-toggle a user's active/inactive status.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manageAccount', $user);

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $actionText = $newStatus === 'active' ? 'activated' : 'deactivated';

        return back()->with('success', "User {$user->full_name} has been {$actionText}.");
    }
}
