<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChefService;

class ChefServicePolicy
{
    /**
     * Determine if user can view any chef services (only their own)
     */
    public function viewAny(User $user): bool
    {
        return $user->chef !== null;
    }

    /**
     * Determine if user can view a specific chef service
     */
    public function view(User $user, ChefService $chefService): bool
    {
        // User must be a chef and the service must belong to their chef account
        if ($user->chef === null) {
            return false;
        }

        return $chefService->chef_id === $user->chef->id;
    }

    /**
     * Determine if user can create a chef service
     */
    public function create(User $user): bool
    {
        // Only users with a chef account can create services
        return $user->chef !== null;
    }

    /**
     * Determine if user can update a chef service
     */
    public function update(User $user, ChefService $chefService): bool
    {
        // User must be a chef and the service must belong to their chef account
        if ($user->chef === null) {
            return false;
        }

        return $chefService->chef_id === $user->chef->id;
    }

    /**
     * Determine if user can delete a chef service
     */
    public function delete(User $user, ChefService $chefService): bool
    {
        // User must be a chef and the service must belong to their chef account
        if ($user->chef === null) {
            return false;
        }

        return $chefService->chef_id === $user->chef->id;
    }

    /**
     * Determine if user can activate a chef service
     */
    public function activate(User $user, ChefService $chefService): bool
    {
        // User must be a chef and the service must belong to their chef account
        if ($user->chef === null) {
            return false;
        }

        return $chefService->chef_id === $user->chef->id;
    }

    /**
     * Determine if user can deactivate a chef service
     */
    public function deactivate(User $user, ChefService $chefService): bool
    {
        // User must be a chef and the service must belong to their chef account
        if ($user->chef === null) {
            return false;
        }

        return $chefService->chef_id === $user->chef->id;
    }
}
