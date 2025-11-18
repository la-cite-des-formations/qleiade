<?php

declare(strict_types=1);

namespace Admin\Orchid;

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

        // La directive personnalisée @hasAccess n'est plus nécessaire.
        // La méthode ->permission() sur les items de menu gère
        // automatiquement l'affichage ou non des liens.
        // Nous la laissons vide pour le moment.
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        /**
         * Ceci est la nouvelle façon de déclarer le menu (v13+).
         * C'est déclaratif, plus facile à lire et à maintenir.
         * Nous n'avons plus besoin du fichier PlatformConfig.php pour le menu.
         */
        return [
            // 1. Lien "go_home"
            Menu::make(__('go_home'))
                ->icon('home')
                ->route('home') // Route personnalisée (probablement vers votre React)
                ->title(__("Another page")), // Titre du premier groupe

            // 2. Groupe "Administer"
            Menu::make(__('quality_labels'))
                ->icon('badge')
                ->route('platform.quality.quality_labels')
                ->permission('platform.quality.quality_labels')
                ->title(__("Administer")), // Titre du second groupe

            Menu::make(__('wealths'))
                ->icon('docs')
                ->route('platform.quality.wealths')
                ->permission('platform.quality.wealths'),

            Menu::make(__('tags'))
                ->icon('tag')
                ->route('platform.quality.tags')
                ->permission('platform.quality.tags'),

            Menu::make(__('actions_stage'))
                ->icon('layers')
                ->route('platform.quality.actions')
                ->permission('platform.quality.actions'),

            // 3. Groupe "Droits d'accès"
            Menu::make(__('Utilisateurs'))
                ->icon('user')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__("Droits d'accès")), // Titre du troisième groupe

            Menu::make(__('Roles'))
                ->icon('lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
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
        // Cette section est parfaite, nous la gardons telle quelle.
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
