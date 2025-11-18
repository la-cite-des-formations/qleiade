<?php

namespace Admin\Orchid\Screens\Action;

use Illuminate\Http\Request;
// RECONSTRUCTION : Auth n'est plus nécessaire ici
// use Illuminate\Support\Facades\Auth;

use Models\Action;
use Admin\Orchid\Layouts\Action\ListLayout;
use Admin\Orchid\Layouts\Action\FiltersLayout;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;


class ListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Cette requête est déjà parfaite (v13+)
        return [
            'actions' => Action::with(['stage'])
                ->filters()
                ->filtersApplySelection(Filterslayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('actions');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('actions_list_description');
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.actions',
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('plus')
                ->route('platform.quality.actions.create')
                // RECONSTRUCTION : Remplacement de la vérification v11 par la v13+
                // (Maintenant que notre AuthServiceProvider est corrigé)
                ->canSee(request()->user()->can('platform.quality.actions.create')),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        // Parfait, pas de changement
        return [
            FiltersLayout::class,
            ListLayout::class
        ];
    }

    /**
     * @param Request $request
     */
    public function remove(Request $request): void
    {
        // Parfait, pas de changement
        $action = Action::findOrFail($request->get('id'));

        $action->delete();
        Toast::info(__('Action_was_removed'));
    }
}
