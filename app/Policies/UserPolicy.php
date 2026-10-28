<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the authenticated user can view the user list.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->isAdmin();
    }

    /**
     * Determine whether the authenticated user can view a user profile.
     */
    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->isAdmin() || $authUser->id === $targetUser->id;
    }

    /**
     * Determine whether the authenticated user can update a user's role or status.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->isAdmin();
    }

    /**
     * Determine whether the authenticated user can change a user's role or status
     * without locking themselves out of their own admin account.
     */
    public function manageAccount(User $authUser, User $targetUser): bool
    {
        return $authUser->isAdmin() && $authUser->id !== $targetUser->id;
    }
}
