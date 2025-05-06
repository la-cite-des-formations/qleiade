<?php

namespace Admin\Orchid\Layouts\Parts;

use Admin\Orchid\Fields\SearchField;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;
use Models\Unit;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Switcher;

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
                SearchField::make('search.keyword', __('search_input'))
                    ->title(__('search_input'))
                    ->type("search")
                    ->placeholder(__('What to search...'))
                    ->class("form-control rounded")
                    ->id("search_wealths_elements"),

                Relation::make('search.unit', __('unit'))
                    ->fromModel(Unit::class, 'label', 'id')
                    ->displayAppend('full')
                    ->chunk(50)
                    ->title(__('unit_select_title')),

                Switcher::make('search.archived', __('archived'))
                    ->title(__('archived')),
            ]),
            // ->set('align', ''),
            Group::make([
                Link::make('reinitialize', __('reinitialize'))
                    ->icon('reload')
                    ->class('btn btn-secondary btn-initialize')
                    ->route('platform.quality.wealths')
            ])
        ];
    }
}
