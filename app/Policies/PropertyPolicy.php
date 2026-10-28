<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    /**
     * Anyone can view property listings.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone can view a single property listing.
     */
    public function view(?User $user, Property $property): bool
    {
        return true;
    }

    /**
     * Only active property owners, agents, or admins can create listings.
     */
    public function create(User $user): bool
    {
        return $user->status === 'active' && $user->canListProperties();
    }

    /**
     * Owners/agents can update only their own properties; admins can update any property.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->owner_id === $user->id;
    }

    /**
     * Owners/agents can delete only their own properties; admins can delete any property.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->owner_id === $user->id;
    }
}
