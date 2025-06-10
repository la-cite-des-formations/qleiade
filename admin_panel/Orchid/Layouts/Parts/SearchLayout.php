<?php

namespace Admin\Orchid\Layouts\Parts;

use Models\Indicator;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;
use Models\Unit;
use Models\WealthType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class SearchLayout extends Rows
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
        return [
            Group::make([
                Input::make('search.keyword', __('search_input'))
                    ->type('search')
                    ->title(__('search_input'))
                    ->placeholder(__('Search...'))
                    ->value(request('search.wealth_type'))
            ]),

            Group::make([
                Relation::make('search.units', __('units'))
                    ->fromModel(Unit::class, 'label', 'id')
                    ->displayAppend('full')
                    ->multiple()
                    ->chunk(50)
                    ->title(__('units'))
                    ->placeholder(__('Select...'))
                    ->value(request('search.units') ? explode(',', request('search.units')[0]) : []),

                Relation::make('search.indicators', __('indicators'))
                    ->fromModel(Indicator::class, 'label', 'id')
                    ->multiple()
                    ->displayAppend('full')
                    ->chunk(50)
                    ->title(__('indicators'))
                    ->placeholder(__('Select...'))
                    ->value(request('search.indicators') ? explode(',', request('search.indicators')[0]) : []),
            ]),

            Group::make([
                Relation::make('search.wealth_type', __('wealth_type'))
                    ->fromModel(WealthType::class, 'label', 'id')
                    ->chunk(50)
                    ->title(__('wealth_type'))
                    ->placeholder(__('Select...'))
                    ->value(request('search.wealth_type')),

                Select::make('search.conformity')
                    ->title(__('conformity_level'))
                    ->options([
                        'essentielle' => __('conformity_level_essential'),
                        'complémentaire' => __('conformity_level_complementary'),
                    ])
                    ->empty(__('Select...'), 0)
                    ->value(request('search.conformity')),

                Link::make('reinitialize', __('reinitialize'))
                    ->icon('reload')
                    ->class('btn btn-outline-secondary')
                    ->route('platform.quality.wealths')
            ])->alignEnd(),

            // Champ caché pour le sort
            Input::make('sort')
                ->type('hidden')
                ->value(request('sort', '')),
        ];
    }
}
