<?php

declare(strict_types=1);

namespace Admin\Orchid\Layouts\Action;

use Models\Stage;
use Models\Unit;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

//DOC NEW ORCHID FORM: add new layout if necessary

class EditLayout extends Rows
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'action';

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('action.id')
                ->hidden(),

            Input::make('action.label')
                ->title(__('action_label'))
                ->placeholder('cap 3 ans')
                ->required(),

            Input::make('action.order')
                ->title(__("action_order"))
                ->type('number'),


            Relation::make('action.stage')
                ->fromModel(Stage::class, 'label')
                ->chunk(50)
                ->required()
                ->title(__('stage')),

            Relation::make('action.unit')
                ->fromModel(Unit::class, 'label')
                ->chunk(50)
                ->required()
                ->multiple()
                ->title(__('unit')),

            Quill::make('action.description')
                ->title('Description')
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
