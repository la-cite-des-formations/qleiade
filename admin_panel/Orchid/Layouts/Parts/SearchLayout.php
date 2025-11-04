<?php

namespace Admin\Orchid\Layouts\Parts;

use Models\Indicator;
use Models\QualityLabel;
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
        // Charge et tri des options

        // Options pour les services associés
        $unitOptions = Unit::all()
            ->sortBy('name')
            ->mapWithKeys(function ($unit) {
                return [$unit->id => $unit->full];
            });

        // Options pour l'importance de la preuve
        $conformityOptions = [
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
                    ->value(request('search.keyword'))
            ]),

            Group::make([
                Select::make('search.units', __('units'))
                    ->options($unitOptions)
                    ->displayAppend('full')
                    ->multiple()
                    ->title(__('units'))
                    ->set('data-placeholder', __('Select...'))
                    ->value(request('search.units') ? explode(',', request('search.units')[0]) : []),

                Select::make('search.quality_label', __('quality_label'))
                    // ->fromModel(QualityLabel::class, 'name', 'id')
                    ->options($qualityLabelOptions)
                    ->title(__('quality_label'))
                    ->set('data-placeholder', __('Select...'))
                    ->allowEmpty()
                    ->value(request('search.quality_label')),

                Relation::make('search.indicators', __('indicators'))
                    ->fromModel(Indicator::class, 'indicator.label', 'id')
                    ->multiple()
                    ->title(__('indicators'))
                    ->placeholder(__('Select...'))

                    // AFFICHE l'accesseur 'full' (1.1 . Label)
                    ->displayAppend('full')

                    // DÉCLENCHE l'AJAX quand 'quality_label' change
                    ->dependsOn('search.quality_label')

                    // APPELLE notre nouveau scope pour filtrer ET trier
                    ->applyScope('byQualityLabelAndSort')

                    // (Le tri des valeurs au chargement ne fonctionnera pas,
                    // c'est notre compromis à cause du bug Orchid)
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
                    ->options($conformityOptions)
                    ->set('data-placeholder', __('Select...'))
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
