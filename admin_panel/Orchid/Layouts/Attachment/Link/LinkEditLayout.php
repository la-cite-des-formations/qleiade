<?php

namespace Admin\Orchid\Layouts\Attachment\Link;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Select;

class LinkEditLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        $options = [
            'web' => __('Site web'),
            'google' => __('Répertoire google'),
            'autre' => __('Autre')
        ];

        return [

            // "attachment_type": "link",
            // "link_type": "web",
            // "url": "https://www.cite-formations-tours.fr/",
            // "created_at": "2022-06-22"
            Select::make('attachment.link.type', __('attachment.link.type'))
                ->options($options)
                ->title(__('select_link_type'))
                ->empty(__('select_link_type')),

            Input::make('attachment.link.url', __("attachment.link.url"))
                ->type('url')
                ->title(__('url'))
                ->placeholder("https://example.com")
                ->pattern("https://.*"),
        ];
    }
}
