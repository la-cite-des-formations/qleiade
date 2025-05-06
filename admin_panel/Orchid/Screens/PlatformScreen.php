<?php

declare(strict_types=1);

namespace Admin\Orchid\Screens;

use Admin\Orchid\PlatformConfig;
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
        return [
            'administerItems' => PlatformConfig::getAdminMenuItemsByTitle('Administer'),
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
        return [
            Layout::view('welcome_admin'),
            Layout::modal('Language', Layout::view('partials.language_switcher'))->title(__('select_language')),
        ];
    }
}
