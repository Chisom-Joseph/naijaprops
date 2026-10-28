<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\User;

class EnquiryPolicy
{
    /**
     * Any active authenticated user can view their enquiries inbox/sent list.
     */
    public function viewAny(User $user): bool
    {
        return $user->status === 'active';
    }

    /**
     * Sender, property owner/agent, or admin can view an enquiry.
     */
    public function view(User $user, Enquiry $enquiry): bool
    {
        return $user->isAdmin()
            || $enquiry->user_id === $user->id
            || $enquiry->property?->owner_id === $user->id;
    }

    /**
     * Any active user can send an enquiry about a property they do not own.
     */
    public function create(User $user, Property $property): bool
    {
        return $user->status === 'active' && $property->owner_id !== $user->id;
    }

    /**
     * Only the property owner/agent or an admin can update the enquiry status (pending, replied, closed).
     */
    public function update(User $user, Enquiry $enquiry): bool
    {
        return $user->isAdmin() || $enquiry->property?->owner_id === $user->id;
    }

    /**
     * The sender, property owner/agent, or admin can delete an enquiry.
     */
    public function delete(User $user, Enquiry $enquiry): bool
    {
        return $user->isAdmin()
            || $enquiry->user_id === $user->id
            || $enquiry->property?->owner_id === $user->id;
    }
}
