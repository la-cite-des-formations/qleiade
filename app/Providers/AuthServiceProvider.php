<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \Models\User::class => \App\Policies\UserPolicy::class,
        \Models\Unit::class => \App\Policies\UnitPolicy::class,
        \Models\Wealth::class => \App\Policies\WealthPolicy::class,
        \Models\Action::class => \App\Policies\ActionPolicy::class,
        \Models\Criteria::class => \App\Policies\CriteriaPolicy::class,
        \Models\Indicator::class => \App\Policies\IndicatorPolicy::class,
        \Models\QualityLabel::class => \App\Policies\QualityLabelPolicy::class,
        \Models\Stage::class => \App\Policies\StagePolicy::class,
        \Models\Tag::class => \App\Policies\TagPolicy::class,
        \Models\WealthType::class => \App\Policies\WealthTypePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // SuperAdmin : accès total
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super-Admin') ? true : null;
        });

        // Bridge pour les permissions React (public_)
        // On vérifie Spatie d'abord (via le système natif de Laravel car Spatie s'y branche)
        // puis on fallback sur la colonne JSON permissions (Orchid Legacy)
        Gate::after(function ($user, $ability, $result) {
            if ($result) return true;

            if (str_starts_with($ability, 'public_')) {
                return $user->permissions_legacy && isset($user->permissions_legacy[$ability]) && $user->permissions_legacy[$ability];
            }

            return $result;
        });
    }
}
