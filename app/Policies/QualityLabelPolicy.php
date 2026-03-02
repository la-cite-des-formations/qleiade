<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Models\QualityLabel;
use Models\User;

class QualityLabelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('platform.quality.quality_labels');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QualityLabel $qualityLabel): bool
    {
        return $user->can('platform.quality.quality_labels');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('platform.quality.quality_label.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QualityLabel $qualityLabel): bool
    {
        return $user->can('platform.quality.quality_label.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QualityLabel $qualityLabel): bool
    {
        return $user->can('platform.quality.quality_label.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QualityLabel $qualityLabel): bool
    {
        return $user->can('platform.quality.quality_label.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QualityLabel $qualityLabel): bool
    {
        return $user->can('platform.quality.quality_label.edit');
    }
}
