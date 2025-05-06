<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\Dashboard;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Dashboard $dashboard)
    {
        // DOC NEW ORCHID FORM: register new permissions to show in user edit form
        $permissions = ItemPermission::group('public')
            ->addPermission('public_home', __('Accueil'))
            ->addPermission('public_admin', __('Administrer'))
            ->addPermission('public_quality_labels_audit', __('Auditer'))
            ->addPermission('public_quality_labels_dashboard', __('Suivre'));

        $dashboard->registerPermissions($permissions);
    }
}
