<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Models\Action;
use Models\User;

class ActionPolicy
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
    public function view(User $user, Action $action): bool
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
    public function update(User $user, Action $action): bool
    {
        return $user->can('platform.quality.actions.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Action $action): bool
    {
        return $user->can('platform.quality.actions.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Action $action): bool
    {
        return $user->can('platform.quality.actions.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Action $action): bool
    {
        return $user->can('platform.quality.actions.edit');
    }
}
