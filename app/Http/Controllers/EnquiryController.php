<?php

namespace App\Http\Controllers;

use App\Http\Requests\Enquiry\StoreEnquiryRequest;
use App\Http\Requests\Enquiry\UpdateEnquiryStatusRequest;
use App\Models\Enquiry;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    /**
     * Display the enquiries list (received enquiries for owners/agents/admins, and sent enquiries for buyers/tenants).
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Enquiry::class);

        $user = $request->user();

        $defaultTab = $user->canListProperties() ? 'received' : 'sent';
        $tab = $user->canListProperties() && in_array($request->query('tab'), ['received', 'sent'], true)
            ? (string) $request->query('tab')
            : $defaultTab;

        // Base query for enquiries received on the user's properties (or all if admin)
        $receivedBaseQuery = Enquiry::query()
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('property', fn ($q) => $q->where('owner_id', $user->id));
            });

        // Base query for enquiries sent by this user
        $sentBaseQuery = $user->enquiries();

        if ($tab === 'received' && $user->canListProperties()) {
            $query = (clone $receivedBaseQuery)
                ->with(['property.primaryImage', 'property.owner', 'user']);

            if ($request->filled('status') && array_key_exists((string) $request->input('status'), Enquiry::STATUSES)) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('property_id') && is_numeric($request->input('property_id'))) {
                $query->where('property_id', (int) $request->input('property_id'));
            }

            if ($request->filled('search')) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('message', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('full_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        })
                        ->orWhereHas('property', function ($pq) use ($search) {
                            $pq->where('title', 'like', "%{$search}%")
                                ->orWhere('location', 'like', "%{$search}%");
                        });
                });
            }

            $enquiries = $query->latest('created_at')->paginate(10)->withQueryString();

            $stats = [
                'total' => (clone $receivedBaseQuery)->count(),
                'pending' => (clone $receivedBaseQuery)->where('status', 'pending')->count(),
                'replied' => (clone $receivedBaseQuery)->where('status', 'replied')->count(),
                'closed' => (clone $receivedBaseQuery)->where('status', 'closed')->count(),
                'received_count' => (clone $receivedBaseQuery)->count(),
                'sent_count' => (clone $sentBaseQuery)->count(),
            ];

            $myProperties = $user->isAdmin()
                ? Property::select(['id', 'title', 'location'])->latest()->get()
                : $user->properties()->select(['id', 'title', 'location'])->latest()->get();
        } else {
            $tab = 'sent';

            $query = (clone $sentBaseQuery)
                ->with(['property.primaryImage', 'property.owner', 'user']);

            if ($request->filled('status') && array_key_exists((string) $request->input('status'), Enquiry::STATUSES)) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('search')) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('message', 'like', "%{$search}%")
                        ->orWhereHas('property', function ($pq) use ($search) {
                            $pq->where('title', 'like', "%{$search}%")
                                ->orWhere('location', 'like', "%{$search}%");
                        });
                });
            }

            $enquiries = $query->latest('created_at')->paginate(10)->withQueryString();

            $stats = [
                'total' => (clone $sentBaseQuery)->count(),
                'pending' => (clone $sentBaseQuery)->where('status', 'pending')->count(),
                'replied' => (clone $sentBaseQuery)->where('status', 'replied')->count(),
                'closed' => (clone $sentBaseQuery)->where('status', 'closed')->count(),
                'received_count' => $user->canListProperties() ? (clone $receivedBaseQuery)->count() : 0,
                'sent_count' => (clone $sentBaseQuery)->count(),
            ];

            $myProperties = collect();
        }

        return view('enquiries.index', compact('enquiries', 'stats', 'tab', 'myProperties'));
    }

    /**
     * Store a newly sent enquiry message about a property.
     */
    public function store(StoreEnquiryRequest $request, Property $property): RedirectResponse
    {
        Gate::authorize('create', [Enquiry::class, $property]);

        $validated = $request->validated();

        Enquiry::create([
            'property_id' => $property->id,
            'user_id' => $request->user()->id,
            'message' => trim($validated['message']),
            'status' => 'pending',
        ]);

        $recipientName = $property->owner?->full_name ?? 'the property provider';

        return redirect()
            ->route('properties.show', $property)
            ->with('success', "Your enquiry has been sent to {$recipientName}! You can track its status in your Enquiries inbox.");
    }

    /**
     * Update the status of an enquiry (pending, replied, closed) by the property owner/agent or admin.
     */
    public function updateStatus(UpdateEnquiryStatusRequest $request, Enquiry $enquiry): RedirectResponse
    {
        $enquiry->loadMissing('property');

        Gate::authorize('update', $enquiry);

        $validated = $request->validated();

        $enquiry->update([
            'status' => $validated['status'],
        ]);

        $statusLabel = Enquiry::STATUSES[$validated['status']] ?? ucfirst($validated['status']);

        return back()->with('success', "Enquiry status updated to {$statusLabel}.");
    }

    /**
     * Delete an enquiry record.
     */
    public function destroy(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $enquiry->loadMissing('property');

        Gate::authorize('delete', $enquiry);

        $enquiry->delete();

        return back()->with('success', 'Enquiry removed.');
    }
}
