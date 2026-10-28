<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Property;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Store a newly submitted report against a suspicious property listing.
     */
    public function store(StoreReportRequest $request, Property $property): RedirectResponse
    {
        Gate::authorize('create', [Report::class, $property]);

        $user = $request->user();
        $validated = $request->validated();

        // Prevent duplicate open/pending reports from the same user on the same property
        $existingPending = Report::where('property_id', $property->id)
            ->where('reported_by', $user->id)
            ->whereIn('status', ['pending', 'reviewed'])
            ->first();

        if ($existingPending) {
            $existingPending->update([
                'reason' => trim($validated['reason']),
            ]);

            return redirect()
                ->route('properties.show', $property)
                ->with('success', 'Your existing report for this property has been updated and is awaiting administrator review.');
        }

        Report::create([
            'property_id' => $property->id,
            'reported_by' => $user->id,
            'reason' => trim($validated['reason']),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Thank you for helping keep NaijaProps safe. Your report has been submitted to our administrators for review.');
    }
}
