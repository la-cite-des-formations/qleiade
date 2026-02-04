<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Models\Stage;
use Models\User;

class StagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('platform.quality.actions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Stage $stage): bool
    {
        return $user->can('platform.quality.actions');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('platform.quality.actions.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Stage $stage): bool
    {
        return $user->can('platform.quality.actions.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Stage $stage): bool
    {
        return $user->can('platform.quality.actions.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Stage $stage): bool
    {
        return $user->can('platform.quality.actions.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Stage $stage): bool
    {
        return $user->can('platform.quality.actions.edit');
    }
}
