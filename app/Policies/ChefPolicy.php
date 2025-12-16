<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chef;

class ChefPolicy
{
    /**
     * Determine if the user can view any chefs.
     */
    public function viewAny(?User $user): bool
    {
        // Allow any user, authenticated or guest, to view chefs
        return true;
    }

    /**
     * Determine if the user can create chefs.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a chef profile
    }

    /**
     * Determine if the user can view the chef.
     */
    public function view(?User $user, Chef $chef): bool
    {
        // Allow any user (authenticated or guest) to view chefs — same behavior as viewAny.
        return true;
    }

    /**
     * Determine if the user can update the chef.
     */
    public function update(User $user, Chef $chef): bool
    {
        return $chef->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the chef.
     */
    public function delete(User $user, Chef $chef): bool
    {
        return $chef->user_id === $user->id;
    }

    /**
     * Determine if the user can activate the chef.
     */
    public function activate(User $user, Chef $chef): bool
    {
        return $chef->user_id === $user->id;
    }

    /**
     * Determine if the user can deactivate the chef.
     */
    public function deactivate(User $user, Chef $chef): bool
    {
        return $chef->user_id === $user->id;
    }
}