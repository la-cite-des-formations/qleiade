<?php

declare(strict_types=1);

namespace Admin\Orchid\Layouts\Criteria;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Layouts\Rows;

class EditLayout extends Rows
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'criteria';

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('criteria.id')
                ->hidden(),

            Input::make('criteria.quality_label_id')
                ->hidden(),

            Input::make('criteria.label')
                ->title(__('criteria_label'))
                ->required(),

            Input::make('criteria.order')
                ->title(__("criteria_order"))
                ->type('number'),

            Quill::make('criteria.description')
                ->title('Description'),
        ];
    }
}
