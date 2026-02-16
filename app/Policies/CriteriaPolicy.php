<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Models\Criteria;
use Models\User;

class CriteriaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('platform.quality.criterias');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Criteria $criteria): bool
    {
        return $user->can('platform.quality.criterias');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('platform.quality.criteria.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Criteria $criteria): bool
    {
        return $user->can('platform.quality.criteria.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Criteria $criteria): bool
    {
        return $user->can('platform.quality.criteria.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Criteria $criteria): bool
    {
        return $user->can('platform.quality.criteria.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Criteria $criteria): bool
    {
        return $user->can('platform.quality.criteria.edit');
    }
}
