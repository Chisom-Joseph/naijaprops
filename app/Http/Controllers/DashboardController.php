<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Property;
use App\Services\PropertyRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly PropertyRecommendationService $recommendationService
    ) {}

    /**
     * Display the role-aware dashboard for owners, agents, and buyers/tenants.
     * Admins are redirected to the dedicated Admin Dashboard.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->canListProperties()) {
            // Property Owner / Real Estate Agent overview
            $properties = $user->properties()
                ->with('primaryImage')
                ->latest()
                ->take(6)
                ->get();

            $receivedEnquiries = Enquiry::whereHas('property', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
                ->with(['property', 'user'])
                ->latest('created_at')
                ->take(5)
                ->get();

            $stats = [
                'total_listings' => $user->properties()->count(),
                'available_listings' => $user->properties()->where('status', 'available')->count(),
                'pending_enquiries' => Enquiry::whereHas('property', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->where('status', 'pending')->count(),
                'saved_count' => $user->savedProperties()->count(),
            ];

            return view('dashboard.index', compact('user', 'properties', 'receivedEnquiries', 'stats'));
        }

        // Buyer / Tenant overview
        $savedProperties = $user->savedProperties()
            ->with(['property.primaryImage', 'property.owner'])
            ->latest('created_at')
            ->take(6)
            ->get();

        $myEnquiries = $user->enquiries()
            ->with(['property.owner'])
            ->latest('created_at')
            ->take(5)
            ->get();

        $sessionPreferences = (array) $request->session()->get('ai_preferences', []);
        $recentlyViewedIds = (array) $request->session()->get('recently_viewed_properties', []);

        $aiResult = $this->recommendationService->getRecommendations(
            user: $user,
            explicitPreferences: $sessionPreferences,
            recentlyViewedIds: $recentlyViewedIds,
            limit: 3
        );

        $recommendedProperties = $aiResult['recommendations'];
        $recommendationProfile = $aiResult['profile'];
        $recommendedPreview = $recommendedProperties->pluck('property');

        $savedPropertyIds = $user->savedProperties()->pluck('property_id')->all();

        $stats = [
            'saved_count' => $user->savedProperties()->count(),
            'enquiries_sent' => $user->enquiries()->count(),
            'available_properties' => Property::where('status', 'available')->count(),
        ];

        return view('dashboard.index', compact(
            'user',
            'savedProperties',
            'myEnquiries',
            'recommendedProperties',
            'recommendationProfile',
            'recommendedPreview',
            'savedPropertyIds',
            'stats'
        ));
    }
}
