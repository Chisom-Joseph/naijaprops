<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SavedProperty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedPropertyController extends Controller
{
    /**
     * Display a paginated list of properties saved by the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = $user->savedProperties()
            ->with([
                'property' => fn ($q) => $q->with(['primaryImage', 'owner'])->withCount('images'),
            ])
            ->whereHas('property');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('property_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('transaction_type') && in_array($request->input('transaction_type'), ['sale', 'rent'], true)) {
            $txType = $request->input('transaction_type');
            $query->whereHas('property', fn ($q) => $q->where('transaction_type', $txType));
        }

        $savedProperties = $query->latest('created_at')->paginate(9)->withQueryString();

        $baseSavedQuery = $user->savedProperties()->whereHas('property');

        $stats = [
            'total' => (clone $baseSavedQuery)->count(),
            'for_sale' => (clone $baseSavedQuery)->whereHas('property', fn ($q) => $q->where('transaction_type', 'sale'))->count(),
            'for_rent' => (clone $baseSavedQuery)->whereHas('property', fn ($q) => $q->where('transaction_type', 'rent'))->count(),
        ];

        return view('saved-properties.index', compact('savedProperties', 'stats'));
    }

    /**
     * Toggle saving/unsaving a property listing for the authenticated user.
     */
    public function toggle(Request $request, Property $property): RedirectResponse
    {
        $user = $request->user();

        $existing = SavedProperty::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', "Removed \"{$property->title}\" from your saved properties.");
        }

        SavedProperty::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);

        return back()->with('success', "Saved \"{$property->title}\" to your bookmarks!");
    }

    /**
     * Remove a specific property from the authenticated user's saved list.
     */
    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $request->user()
            ->savedProperties()
            ->where('property_id', $property->id)
            ->delete();

        return back()->with('success', "Removed \"{$property->title}\" from your saved properties.");
    }
}
