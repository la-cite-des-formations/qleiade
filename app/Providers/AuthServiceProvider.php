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
        // 'Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // --- C'EST LE CODE MAGIQUE ---
        // Nous disons au Gate de Laravel (qui gère @can) :
        // "Avant de vérifier une permission, exécute ce code d'abord"
        Gate::before(function ($user, $ability) {

            // 1. On vérifie si l'utilisateur a la méthode 'hasAccess'
            //    (ce qui prouve que c'est un utilisateur Orchid)
            if (method_exists($user, 'hasAccess')) {

                // 2. On délègue la vérification au système natif d'Orchid
                //    (qui lit les cases qu'on a cochées dans l'admin)
                return $user->hasAccess($ability) ? true : null;
            }

            // Ce n'est pas un utilisateur Orchid, on laisse Laravel se débrouiller
            return null;
        });
        // --- FIN DU CODE MAGIQUE ---
    }
}
