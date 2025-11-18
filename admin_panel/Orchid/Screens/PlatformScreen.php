<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens;

// Nous n'avons plus besoin de PlatformConfig
// use Admin\Orchid\PlatformConfig;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;

class PlatformScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        /*
         * RECONSTRUCTION :
         * Au lieu d'appeler l'ancien fichier PlatformConfig, nous définissons
         * les "welcome cards" directement ici. C'est plus propre et supprime la dépendance.
         * La vue 'welcome_admin.blade.php' attend un tableau d'objets avec
         * les propriétés 'permission', 'icon', 'route', et 'welcomeLabel'.
         */
        $administerItems = [
            (object) [
                'welcomeLabel' => 'welcome_quality_labels',
                'icon'         => 'badge',
                'route'        => 'platform.quality.quality_labels',
                'permission'   => 'platform.quality.quality_labels',
            ],
            (object) [
                'welcomeLabel' => 'welcome_wealths_title',
                'icon'         => 'docs',
                'route'        => 'platform.quality.wealths',
                'permission'   => 'platform.quality.wealths',
            ],
            (object) [
                'welcomeLabel' => 'welcome_labels_title',
                'icon'         => 'tag',
                'route'        => 'platform.quality.tags',
                'permission'   => 'platform.quality.tags',
            ],
            (object) [
                'welcomeLabel' => 'welcome_actions_title',
                'icon'         => 'layers',
                'route'        => 'platform.quality.actions',
                'permission'   => 'platform.quality.actions',
            ],
        ];


        return [
            'administerItems' => $administerItems,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Home');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('welcome_description');
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        // Cette partie est correcte et n'a pas besoin de changer
        return [
            ModalToggle::make('Language')
                ->modal('Language')
                ->icon('bubble'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        // Cette partie est correcte et n'a pas besoin de changer
        return [
            Layout::view('welcome_admin'),
            Layout::modal('Language', Layout::view('partials.language_switcher'))->title(__('select_language')),
        ];
    }
}
