<?php

namespace Admin\Orchid\Screens\Action;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                //it works
                ->canSee(Auth::user()->hasAccess('platform.quality.actions.create')),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
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
        $action = Action::findOrFail($request->get('id'));

        $action->delete();
        Toast::info(__('Action_was_removed'));
    }
}
