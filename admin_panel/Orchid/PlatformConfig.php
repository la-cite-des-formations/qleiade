<?php

namespace Admin\Orchid;

use Orchid\Screen\Actions\Menu;

class PlatformConfig {

    const ADMIN_MENU_ITEMS = [
        'home' => [
            'firstSubItem' => TRUE,
            'name' => 'go_home',
            'icon' => 'home',
            'route' => 'home',
            'title' => "Another page",
        ],
        'quality_labels' => [
            'firstSubItem' => TRUE,
            'name' => 'quality_labels',
            'welcomeLabel' => 'welcome_quality_labels',
            'icon' => 'badge',
            'route' => 'platform.quality.quality_labels',
            'permission' => 'platform.quality.quality_labels',
            'title' => "Administer",
        ],
        'wealths' => [
            'firstSubItem' => FALSE,
            'name' => 'wealths',
            'welcomeLabel' => 'welcome_wealths_title',
            'icon' => 'docs',
            'route' => 'platform.quality.wealths',
            'permission' => 'platform.quality.wealths',
            'title' => "Administer",
        ],
        'tags' => [
            'firstSubItem' => FALSE,
            'name' => 'tags',
            'welcomeLabel' => 'welcome_labels_title',
            'icon' => 'tag',
            'route' => 'platform.quality.tags',
            'permission' => 'platform.quality.tags',
            'title' => "Administer",
        ],
        'actions' => [
            'firstSubItem' => FALSE,
            'name' => 'actions_stage',
            'welcomeLabel' => 'welcome_actions_title',
            'icon' => 'layers',
            'route' => 'platform.quality.actions',
            'permission' => 'platform.quality.actions',
            'title' => "Administer",
        ],
        'users' => [
            'firstSubItem' => TRUE,
            'name' => 'Utilisateurs',
            'icon' => 'user',
            'route' => 'platform.systems.users',
            'permission' => 'platform.systems.users',
            'title' => "Droits d'accès",
        ],
        'roles' => [
            'firstSubItem' => FALSE,
            'name' => 'Roles',
            'icon' => 'lock',
            'route' => 'platform.systems.roles',
            'permission' => 'platform.systems.roles',
            'title' => "Droits d'accès",
        ],
    ];

    public static function getAdminMenuItem($item) {
        return (object) static::ADMIN_MENU_ITEMS[$item];
    }

    public static function getAdminMenuItems() {
        return array_map(function ($item) { return (object) $item; }, static::ADMIN_MENU_ITEMS);
    }

    public static function getAdminMenuItemsByTitle($title) {
        $items = [];

        foreach (static::ADMIN_MENU_ITEMS as $item) {
            if ($item['title'] == $title) {
                $items[] = (object) $item;
            }
        };

        return $items;
    }

    public static function getAdminMenu() {
        return array_map(
            function ($item) {
                $orchidMenuItem = Menu::make($item->name)
                    ->icon($item->icon)
                    ->route($item->route);

                if ($item->firstSubItem) {
                    $orchidMenuItem->title(__($item->title));
                }

                if (isset($item->permission)) {
                    $orchidMenuItem->permission($item->permission);
                }

                return $orchidMenuItem;
            },
            static::getAdminMenuItems()
        );
    }
}
