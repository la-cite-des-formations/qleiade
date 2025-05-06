<?php

declare(strict_types=1);

namespace Admin\Orchid\Layouts\Wealth;

use Models\WealthType;
use App\Http\Traits\WithAttachments;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\DateTimer;

class EditLayout extends Rows
{
    use WithAttachments;
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'wealth';

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('wealth.id')
                ->hidden(),

            Input::make('wealth.name')
                ->title(__('wealth_name'))
                ->placeholder(__('wealth_name'))
                ->required(),

            Relation::make('wealth.wealth_type')
                ->fromModel(WealthType::class, 'label', 'id')
                // ->fromQuery(User::where('balance', '!=', '0'), 'email')
                ->title(__('wealth_type_select_title'))
                ->required()
                ->disabled(!$this->canEditAttachment($this->query))
                ->help(__('wealth_type_help')),

            Select::make('wealth.conformity_level')
                ->options([
                    '' => "",
                    '100'   => __('Essentielle'),
                    '0' => __('Complémentaire'),
                ])
                ->title(__('conformity_level')),

            DateTimer::make('wealth.validity_date')
                ->title(__('wealth_validity_date'))
                // ->required()
                ->allowInput()
                ->format('d-m-Y')
                ->min(now()),

            Quill::make('wealth.description')
                ->title(__('Description'))
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
