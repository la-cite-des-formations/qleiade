<?php

namespace Admin\Orchid\Layouts\Attachment\Ypareo;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Quill;

class YpareoEditlayout extends Rows
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
        // "attachment_type": "ypareo",
        // "ypareo_type": "process",
        // "process": "Lorem ipsum ..."
        $options = [
            'process' => 'process',
            'request' => 'request',
        ];
        return [
            // Select::make(__('attachment.ypareo.type'))
            //     ->options($options)
            //     ->title(__('select_ypareo_type'))
            //     ->empty(__('select_ypareo_type')),

            Quill::make('attachment.ypareo.process')
                ->title(__('how_to_find_ypareo_info'))
                ->popover(__('select_ypareo_tooltype'))
                ->toolbar(["text", "color", "header", "list", "format"])
        ];
    }
}
