<?php

namespace Admin\Orchid\Layouts\Parts;

use Models\Indicator;
use Models\Unit;
use Models\WealthType;
use Models\QualityLabel;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Actions\Link;

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
        // Charge et tri des options

        // Options pour les services associés
        $unitOptions = Unit::all()
            ->sortBy('name')
            ->mapWithKeys(function ($unit) {
                return [$unit->id => $unit->full];
            });

        // Options pour l'importance de la preuve
        $conformityOptions = [
            '' => __('Select...'),
            'essentielle' => __('conformity_level_essential'),
            'complémentaire' => __('conformity_level_complementary'),
        ];

        // Options pour le label qualité
        $qualityLabelOptions = QualityLabel::all()
            ->sortBy('name')
            ->pluck('name', 'id');

        return [
            Group::make([
                Input::make('search.keyword', __('search_input'))
                    ->type('search')
                    ->title(__('search_input'))
                    ->placeholder(__('Search...'))
            ]),

            Group::make([
                Select::make('search.units', __('units'))
                    ->options($unitOptions)
                    ->multiple()
                    ->title(__('units'))
                    ->placeholder(__('Select...')),

                Relation::make('search.quality_label', __('quality_label'))
                    ->fromModel(QualityLabel::class, 'name', 'id') // <-- Définit le modèle et les champs
                    ->title(__('quality_label'))
                    ->placeholder(__('Select...')),

                Relation::make('search.indicators', __('indicators'))
                    ->fromModel(Indicator::class, 'indicator.label', 'id')
                    ->multiple()
                    ->title(__('indicators'))
                    ->placeholder(__('Select...'))
                    ->displayAppend('full')
                    ->dependsOn('search.quality_label')
                    ->applyScope('byQualityLabelAndSort')
            ]),

            Group::make([
                Relation::make('search.wealth_type', __('wealth_type'))
                    ->fromModel(WealthType::class, 'label', 'id')
                    ->title(__('wealth_type'))
                    ->placeholder(__('Select...')),

                Select::make('search.conformity')
                    ->options($conformityOptions)
                    ->title(__('conformity_level'))
                    ->placeholder(__('Select...')),

                Link::make('reinitialize', __('reinitialize'))
                    ->icon('reload')
                    ->route('platform.quality.wealths')
                    ->class('btn btn-outline-secondary')
            ])->alignEnd(),

            // Champ caché pour le sort
            Input::make('sort')
                ->type('hidden'),
        ];
    }
}
