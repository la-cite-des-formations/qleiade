<?php

namespace Admin\Orchid\Layouts\QualityLabel;

use Illuminate\Support\Str;
use Models\QualityLabel;

use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
// Pas besoin de ModalToggle ici

class ListLayout extends Table
{
    /**
     * Data source.
     * @var string
     */
    protected $target = 'quality_labels';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('label', __('label'))
                ->sort(),

            TD::make('description', __('description'))
                ->sort()
                ->render(function (QualityLabel $qualityLabel) {
                    // Correction du bug d'affichage (gardée)
                    return Str::limit(strip_tags($qualityLabel->description ?? ''), 50);
                }),

            TD::make(__('Actions_form'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (QualityLabel $qualityLabel) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            // RESTAURATION : Le lien v11 d'origine
                            // C'est la clé de tout.
                            Link::make(__('Edit'))
                                ->route('platform.quality.quality_label.edit', $qualityLabel->id)
                                ->icon('pencil')
                                ->canSee(request()->user()->can('platform.quality.quality_label.edit')),


                            Link::make(__('Edit its indciators'))
                                ->route('platform.quality.quality_label.indicators', ["quality_label" => $qualityLabel])
                                ->icon('equalizer'),

                            // Le bouton "Delete" (qui fonctionnait)
                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('qualityLabel_remove_confirmation'))
                                ->method('remove', [
                                    'qualityLabel' => $qualityLabel->id,
                                ]),
                        ]);
                }),
        ];
    }
}
