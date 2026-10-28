<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Only administrators can view the full reports management list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Administrators or the user who filed the report can view it.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || $report->reported_by === $user->id;
    }

    /**
     * Any active authenticated user can report a suspicious property listing they do not own.
     */
    public function create(User $user, Property $property): bool
    {
        return $user->status === 'active' && $property->owner_id !== $user->id;
    }

    /**
     * Only administrators can review and update the status of a report.
     */
    public function update(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only administrators can delete a report record.
     */
    public function delete(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }
}
