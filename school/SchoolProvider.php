<?php

namespace School;

use Illuminate\Support\ServiceProvider;
use School\Adapters\Connecter;
use School\Manager\SchoolManager;

class SchoolProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(SchoolManager::class, function ($app) {
            $instance = new SchoolManager();
            $instance->setConnecter("ypareo", Connecter::make("Ypareo"));

            return $instance;
        });
    }
}
