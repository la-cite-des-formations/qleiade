<?php

namespace Admin\Orchid\Layouts\Wealth;


use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Relation;

use Models\Tag;
use Models\Unit;
use Models\QualityLabel;

class RelationsLayout extends Rows
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
            Relation::make('wealth.unit')
                ->fromModel(Unit::class, 'label', 'id')
                ->displayAppend('full')
                ->required()
                ->chunk(50)
                ->title(__('unit_select_title')),

            Relation::make('wealth.qualityLabel')
                ->fromModel(QualityLabel::class, 'label', 'id')
                ->required()
                ->chunk(50)
                ->title(__('qualityLabel_select_title')),

            Group::make([
                Relation::make('wealth.tags')
                    ->fromModel(Tag::class, 'label')
                    ->multiple()
                    ->chunk(50)
                    ->popover("popover_tags")
                    ->title(__('tag_select_title')),
            ])->fullWidth()
                ->set('align', ''),

            Select::make('wealth.granularity.type')
                ->options([
                    '' => "",
                    'global'   => __('wealth_granularity_global'),
                    'formation' => __('wealth_granularity_formation'),
                    'group' => __('wealth_granularity_group'),
                    'student' => __('wealth_granularity_apprenant'),
                ])
                ->popover(__('popover_granularity'))
                ->required()
                ->title(__('wealth_granularity')),
        ];
    }
}
