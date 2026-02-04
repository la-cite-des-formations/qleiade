<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Models\Indicator;
use Models\User;

class IndicatorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('platform.quality.quality_label.indicator.create') 
            || $user->can('platform.quality.quality_label.indicator.edit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Indicator $indicator): bool
    {
        return $user->can('platform.quality.quality_label.indicator.create') 
            || $user->can('platform.quality.quality_label.indicator.edit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('platform.quality.quality_label.indicator.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Indicator $indicator): bool
    {
        return $user->can('platform.quality.quality_label.indicator.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Indicator $indicator): bool
    {
        return $user->can('platform.quality.quality_label.indicator.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Indicator $indicator): bool
    {
        return $user->can('platform.quality.quality_label.indicator.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Indicator $indicator): bool
    {
        return $user->can('platform.quality.quality_label.indicator.edit');
    }
}
