<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\UpdateReportStatusRequest;
use App\Models\Property;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportManagementController extends Controller
{
    /**
     * Display a filterable, paginated list of all property reports for administrators.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $query = Report::query()
            ->with([
                'property.primaryImage',
                'property.owner',
                'reporter',
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('property', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhereHas('owner', function ($oq) use ($search) {
                                $oq->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('reporter', function ($rq) use ($search) {
                        $rq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && array_key_exists((string) $request->input('status'), Report::STATUSES)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('property_id') && is_numeric($request->input('property_id'))) {
            $query->where('property_id', (int) $request->input('property_id'));
        }

        $reports = $query->latest('created_at')->paginate(10)->withQueryString();

        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'reviewed' => Report::where('status', 'reviewed')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'flagged_properties' => Property::has('reports')->count(),
        ];

        $filteredProperty = $request->filled('property_id') && is_numeric($request->input('property_id'))
            ? Property::find((int) $request->input('property_id'))
            : null;

        return view('admin.reports.index', compact('reports', 'stats', 'filteredProperty'));
    }

    /**
     * Update a report's status and optionally take moderation action on the reported property.
     */
    public function updateStatus(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        Gate::authorize('update', $report);

        $validated = $request->validated();
        $newStatus = $validated['status'];
        $propertyAction = $validated['property_action'] ?? 'none';

        $report->loadMissing('property.images');
        $property = $report->property;

        // If admin chooses to remove the reported property listing altogether, cascade deletes the report too
        if ($propertyAction === 'remove' && $property) {
            $propertyTitle = $property->title;

            foreach ($property->images as $image) {
                if ($image->isLocalFile()) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            $property->delete();

            return back()->with('success', "Reported listing \"{$propertyTitle}\" and its associated reports have been permanently removed.");
        }

        $report->update([
            'status' => $newStatus,
        ]);

        $statusLabel = Report::STATUSES[$newStatus] ?? ucfirst($newStatus);
        $actionSummary = '';

        if ($property && $propertyAction === 'approve') {
            $property->update(['status' => 'available']);
            $actionSummary = " Listing \"{$property->title}\" has been approved (Available).";
        } elseif ($property && $propertyAction === 'reject') {
            $property->update(['status' => 'pending']);
            $actionSummary = " Listing \"{$property->title}\" has been placed on hold (Pending) and hidden from public available search.";
        }

        return back()->with('success', "Report #RP-{$report->id} marked as {$statusLabel}.{$actionSummary}");
    }

    /**
     * Delete/dismiss a report record.
     */
    public function destroy(Report $report): RedirectResponse
    {
        Gate::authorize('delete', $report);

        $report->delete();

        return back()->with('success', 'Report record has been deleted.');
    }
}
