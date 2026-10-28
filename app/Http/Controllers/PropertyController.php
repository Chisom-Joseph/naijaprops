<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyStatusRequest;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a paginated list of property listings with advanced search, filtering, and sorting.
     */
    public function index(SearchPropertyRequest $request): View
    {
        Gate::authorize('viewAny', Property::class);

        $filters = $request->filters();

        $properties = Property::query()
            ->with(['primaryImage', 'owner'])
            ->withCount('images')
            ->filter($filters)
            ->paginate(9)
            ->withQueryString();

        $savedPropertyIds = $request->user()
            ? $request->user()->savedProperties()->pluck('property_id')->all()
            : [];

        return view('properties.index', compact('properties', 'filters', 'savedPropertyIds'));
    }

    /**
     * Display the authenticated owner/agent's own property listings.
     */
    public function myListings(Request $request): View
    {
        Gate::authorize('create', Property::class);

        $user = $request->user();

        $query = $user->properties()
            ->with(['primaryImage', 'images'])
            ->withCount(['images', 'enquiries', 'savedProperties']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && array_key_exists($request->input('status'), Property::STATUSES)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('transaction_type') && in_array($request->input('transaction_type'), ['sale', 'rent'], true)) {
            $query->where('transaction_type', $request->input('transaction_type'));
        }

        $properties = $query->latest()->paginate(8)->withQueryString();

        $stats = [
            'total' => $user->properties()->count(),
            'available' => $user->properties()->where('status', 'available')->count(),
            'pending' => $user->properties()->where('status', 'pending')->count(),
            'closed' => $user->properties()->whereIn('status', ['sold', 'rented'])->count(),
        ];

        return view('properties.my-listings', compact('properties', 'stats'));
    }

    /**
     * Show the form for creating a new property listing.
     */
    public function create(): View
    {
        Gate::authorize('create', Property::class);

        return view('properties.create');
    }

    /**
     * Store a newly created property listing and its uploaded images.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Property::class);

        $validated = $request->validated();

        $property = Property::create([
            'owner_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'property_type' => $validated['property_type'],
            'transaction_type' => $validated['transaction_type'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'bedrooms' => $validated['property_type'] === 'land' ? null : ($validated['bedrooms'] ?? null),
            'bathrooms' => $validated['property_type'] === 'land' ? null : ($validated['bathrooms'] ?? null),
            'status' => $validated['status'] ?? 'available',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $storedPath = $imageFile->store('properties', 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $storedPath,
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Your property listing has been published successfully!');
    }

    /**
     * Display the specified property listing details page.
     */
    public function show(Request $request, Property $property): View
    {
        Gate::authorize('view', $property);

        $property->load(['images', 'owner'])
            ->loadCount(['enquiries', 'savedProperties', 'reports']);

        $similarProperties = Property::with(['primaryImage', 'owner'])
            ->where('id', '!=', $property->id)
            ->where('status', 'available')
            ->where(function ($query) use ($property) {
                $city = trim( collect(explode(',', $property->location))->last() ?? $property->location );
                $query->where('property_type', $property->property_type)
                    ->orWhere('location', 'like', "%{$city}%");
            })
            ->latest()
            ->take(3)
            ->get();

        $isSaved = $request->user()
            ? $request->user()->savedProperties()->where('property_id', $property->id)->exists()
            : false;

        $userEnquiries = $request->user()
            ? $request->user()->enquiries()
                ->where('property_id', $property->id)
                ->latest('created_at')
                ->get()
            : collect();

        $userReports = $request->user()
            ? $request->user()->reports()
                ->where('property_id', $property->id)
                ->latest('created_at')
                ->get()
            : collect();

        return view('properties.show', compact('property', 'similarProperties', 'isSaved', 'userEnquiries', 'userReports'));
    }

    /**
     * Show the form for editing the specified property listing.
     */
    public function edit(Property $property): View
    {
        Gate::authorize('update', $property);

        $property->load(['images', 'owner']);

        return view('properties.edit', compact('property'));
    }

    /**
     * Update the specified property listing and manage its images.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);

        $validated = $request->validated();

        $property->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'property_type' => $validated['property_type'],
            'transaction_type' => $validated['transaction_type'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'bedrooms' => $validated['property_type'] === 'land' ? null : ($validated['bedrooms'] ?? null),
            'bathrooms' => $validated['property_type'] === 'land' ? null : ($validated['bathrooms'] ?? null),
            'status' => $validated['status'],
        ]);

        // Remove selected existing images belonging to this property
        if (! empty($validated['remove_images'])) {
            $imagesToRemove = $property->images()
                ->whereIn('id', $validated['remove_images'])
                ->get();

            foreach ($imagesToRemove as $img) {
                if ($img->isLocalFile()) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }
        }

        // Upload new images to storage/app/public/properties
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $storedPath = $imageFile->store('properties', 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $storedPath,
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Property listing updated successfully.');
    }

    /**
     * Quick-update the availability status of a property listing.
     */
    public function updateStatus(UpdatePropertyStatusRequest $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);

        $validated = $request->validated();
        $property->update(['status' => $validated['status']]);

        $statusLabel = Property::STATUSES[$validated['status']] ?? ucfirst($validated['status']);

        return back()->with('success', "Listing status updated to {$statusLabel}.");
    }

    /**
     * Delete a single image from a property listing.
     */
    public function destroyImage(Property $property, PropertyImage $propertyImage): RedirectResponse
    {
        Gate::authorize('update', $property);

        abort_if($propertyImage->property_id !== $property->id, 404);

        if ($propertyImage->isLocalFile()) {
            Storage::disk('public')->delete($propertyImage->image_path);
        }

        $propertyImage->delete();

        return back()->with('success', 'Property image removed.');
    }

    /**
     * Remove the specified property listing and its stored image files.
     */
    public function destroy(Request $request, Property $property): RedirectResponse
    {
        Gate::authorize('delete', $property);

        $property->load('images');

        foreach ($property->images as $image) {
            if ($image->isLocalFile()) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $title = $property->title;
        $property->delete();

        if ($request->user()->isAdmin() && str_contains((string) url()->previous(), '/admin/')) {
            return redirect()
                ->route('admin.properties.index')
                ->with('success', "Property \"{$title}\" has been deleted.");
        }

        if ($request->user()->canListProperties() && ! $request->user()->isAdmin()) {
            return redirect()
                ->route('properties.my')
                ->with('success', "Your property \"{$title}\" has been deleted.");
        }

        return redirect()
            ->route('properties.index')
            ->with('success', "Property \"{$title}\" has been deleted.");
    }
}
