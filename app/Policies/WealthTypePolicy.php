<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Models\WealthType;
use Models\User;

class WealthTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('platform.quality.wealths');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WealthType $wealthType): bool
    {
        return $user->can('platform.quality.wealths');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('platform.quality.wealth.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WealthType $wealthType): bool
    {
        return $user->can('platform.quality.wealth.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WealthType $wealthType): bool
    {
        return $user->can('platform.quality.wealth.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WealthType $wealthType): bool
    {
        return $user->can('platform.quality.wealth.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WealthType $wealthType): bool
    {
        return $user->can('platform.quality.wealth.edit');
    }
}
