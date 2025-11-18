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
    public $target = 'tag'; // RECONSTRUCTION : On garde le $target v11

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('tag.id') // RECONSTRUCTION : On garde la notation "point" v11
                ->hidden(),

            Input::make('tag.label')
                ->title(__('tag_label'))
                // ->placeholder('cap 3 ans') // RECONSTRUCTION : Bizarrerie supprimée
                ->required(),

            Quill::make('tag.description')
                ->title('Description')
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
