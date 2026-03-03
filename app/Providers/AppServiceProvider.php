<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            //DOC: register ideHelper
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //DOC: Corrige  SQLSTATE[42000]: Syntax error or access violation: 1071 La clé est trop longue. Longueur maximale: 1000
        Schema::defaultStringLength(191);

        //DOC: to switch beetween languages
        view()->composer('partials.language_switcher', function ($view) {
            $view->with('current_locale', app()->getLocale());
            $view->with('available_locales', config('app.available_locales'));
        });


        // Suppress Livewire "Page Expired" popup globally in Filament
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Blade::render('
                <script>
                    document.addEventListener("livewire:init", () => {
                        Livewire.hook("request", ({ fail }) => {
                            fail(({ status, preventDefault }) => {
                                if (status === 419) {
                                    preventDefault()
                                    window.location.reload()
                                }
                            })
                        })
                    })
                </script>
            '),
        );
    }
}
