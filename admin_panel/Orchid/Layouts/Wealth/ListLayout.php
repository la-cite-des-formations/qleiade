<?php

namespace Admin\Orchid\Layouts\Wealth;

use Illuminate\Support\Facades\Auth;

use Models\Wealth;
use App\Http\Traits\WithAttachments;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Admin\Orchid\Layouts\Parts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;


class ListLayout extends Table
{
    use WithAttachments;

    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'wealths';

    /**
     * @return bool
     */
    protected function compact(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function bordered(): bool
    {
        return false;
    }

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('archived', __('archived'))
                ->sort()
                ->render(function (Wealth $wealth) {
                    return View('tools.archived_cell', ['archived' => !is_null($wealth->archived_at)]);
                }),

            TD::make(__('name'))
                ->sort()
                ->render(function (Wealth $wealth) {
                    return $wealth->name;
                }),

            TD::make('Unit', __('wealth_unit'))
                ->sort()
                ->render(function (Wealth $wealth) {
                    return $wealth->unit->name;
                }),

            TD::make(__('wealth_type'))
                ->sort()
                ->render(function (Wealth $wealth) {
                    return $wealth->wealthType->label;
                }),

            TD::make('validity_date', __('wealth_validity_date'))
                ->sort()
                ->render(function (Wealth $wealth) {
                    return $wealth->validity_date;
                }),

            TD::make(__('Actions_form'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Wealth $wealth) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            // Link::make(__('Display'))
                            //     ->icon('eye')
                            //     ->route('platform.quality.wealth.display', ["wealth" => $wealth->id]),

                            Link::make(__('Edit'))
                                ->route('platform.quality.wealth.edit', ["wealth" => $wealth->id, "duplicate" => false])
                                ->icon('pencil')
                                ->canSee(Auth::user()->hasAccess('platform.quality.wealth.edit') && is_null($wealth->archived_at)),

                            Link::make(__('Duplicate'))
                                ->route('platform.quality.wealth.edit', ["wealth" => $wealth->id, "duplicate" => true])
                                ->icon('paste')
                                ->canSee(Auth::user()->hasAccess('platform.quality.wealth.create')),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('wealth_remove_confirmation'))
                                ->method('remove', [
                                    'id' => $wealth->id,
                                ])
                                ->canSee(Auth::user()->hasAccess('platform.quality.wealth.edit') && is_null($wealth->archived_at)),
                        ]);
                }),
        ];
    }
}
