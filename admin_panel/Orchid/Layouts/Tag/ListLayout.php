<?php

namespace Admin\Orchid\Layouts\Tag;

use Illuminate\Support\Facades\Auth; // Gardé pour la compatibilité, mais on change la logique

use Models\Tag;
use Illuminate\Support\Str;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

class ListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    protected $target = 'tags';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('label', __('label')) // RECONSTRUCTION : Simplifié (le render n'est pas nécessaire)
                ->sort(),

            TD::make('description', __('description'))
                ->sort()
                ->render(function (Tag $tag) {
                    // RECONSTRUCTION : Correction du bug d'affichage
                    // strip_tags enlève le HTML (si 'description' vient d'un Quill)
                    // Str::limit coupe proprement à 50 caractères
                    return Str::limit(strip_tags($tag->description ?? ''), 50);
                }),


            TD::make(__('Actions_form'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Tag $tag) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.quality.tags.edit', $tag->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('tag_remove_confirmation'))
                                ->method('remove', [
                                    'id' => $tag->id,
                                ]),
                        ])
                        // RECONSTRUCTION : Remplacement de la vérification v11 par la v13+
                        ->canSee(request()->user()->can('platform.quality.tags.edit'));
                }),
        ];
    }
}
