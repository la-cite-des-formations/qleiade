<?php

namespace Admin\Orchid\Layouts\Action;

use Illuminate\Support\Facades\Auth;

use Models\Action;
use Illuminate\Support\Str;
use Models\Unit;
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
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'actions';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [

            TD::make(__('order'))
                ->sort()
                ->render(function (Action $action) {
                    return $action->order;
                }),

            TD::make(__('label'))
                ->sort()
                ->render(function (Action $action) {
                    return $action->label;
                }),

            TD::make(__('stage'))
                ->sort()
                ->render(function (Action $action) {
                    return $action->stage->label;
                }),
            // TD::make(__('unit'))
            //     ->sort()
            //     ->render(function (Unit $unit) {
            //         return $unit->stage->label;
            //     }),

            // TD::make(__('description'))
            //     ->sort()
            //     ->render(function (Action $action) {
            //         //NOTE : add function getFirstSentenceOfHtml($html) to a trait if anothers
            //         $truncatedDesc = Str::before(Str::after($action->description, '<p>'), '</p>') . ' ...';

            //         return $truncatedDesc;
            //     }),


            TD::make(__('Actions_form'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Action $action) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.quality.actions.edit', $action->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('action_remove_confirmation'))
                                ->method('remove', [
                                    'id' => $action->id,
                                ]),
                        ])
                        ->canSee(Auth::user()->hasAccess('platform.quality.actions.edit'));
                }),
        ];
    }
}
