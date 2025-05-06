<?php

declare(strict_types=1);

namespace Admin\Orchid;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;



class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
        Blade::if('hasAccess', function (string $value) {
            $user = Auth::user();

            if ($user === null) {
                return false;
            }

            return $user->hasAccess($value);
        });
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        //DOC NEW ORCHID FORM: add menu item
        return PlatformConfig::getAdminMenu();
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            Menu::make('Profile')
                ->route('platform.profile')
                ->icon('user'),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
            ItemPermission::group('quality')
                ->addpermission('platform.search', __('search'))
                // wealths admin form (proof)
                ->addPermission('platform.quality.wealths', __('wealths'))
                ->addPermission('platform.quality.wealth.create', __('wealth_create'))
                ->addPermission('platform.quality.wealth.edit', __('wealth_edit'))
                // Tags admin form
                ->addPermission('platform.quality.tags', __('tags'))
                ->addPermission('platform.quality.tags.create', __('tag_create'))
                ->addPermission('platform.quality.tags.edit', __('tag_edit'))
                // Actions admin form
                ->addPermission('platform.quality.actions', __('actions'))
                ->addPermission('platform.quality.actions.create', __('action_create'))
                ->addPermission('platform.quality.actions.edit', __('action_edit'))
                // Quality_labels admin form
                ->addPermission('platform.quality.quality_labels', __('quality_labels'))
                ->addPermission('platform.quality.quality_label.create', __('quality_label_create'))
                ->addPermission('platform.quality.quality_label.edit', __('quality_label_edit'))
                // Indicators admin form
                ->addPermission('platform.quality.quality_label.indicators', __('indicators'))
                ->addPermission('platform.quality.quality_label.indicator.create', __('indicator_create'))
                ->addPermission('platform.quality.quality_label.indicator.edit', __('indicator_edit')),
        ];
    }
}
