<?php

namespace Admin\Orchid\Layouts\Indicator;

use Models\Criteria;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;

class EditLayout extends Rows
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'indicator';
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

        return [
            Input::make('indicator.id')
                ->hidden(),

            Input::make('indicator.label')
                ->title(__('indicator_label'))
                ->required(),

            Select::make('indicator.conformity_level_expected')
                ->options([
                    100   => 'Majeure',
                    0 => 'Mineure',
                ])
                ->title(__('Non conformité')),

            Relation::make('indicator.criteria_id')
                ->fromModel(Criteria::class, 'label', 'id')
                ->applyScope('label', $this->query->get('indicator.quality_label_id'))
                ->required()
                ->chunk(50)
                ->title(__('criteria_select_title')),

            Input::make('indicator.number')
                ->title(__('indicator_number'))
                ->type('number')
                ->max(99)
                ->required(),

            Input::make('indicator.quality_label_id')
                ->hidden(),

            Quill::make('indicator.description')
                ->title('Description')
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
