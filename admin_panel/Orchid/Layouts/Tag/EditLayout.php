<?php

declare(strict_types=1);

namespace Admin\Orchid\Layouts\Tag;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Layouts\Rows;

//DOC NEW ORCHID FORM: add new layout if necessary

class EditLayout extends Rows
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'tag';

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('tag.id')
                ->hidden(),

            Input::make('tag.label')
                ->title(__('tag_label'))
                ->placeholder('cap 3 ans')
                ->required(),

            Quill::make('tag.description')
                ->title('Description')
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
