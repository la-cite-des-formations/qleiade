<?php

namespace Admin\Orchid\Layouts\Wealth;

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
                // RECONSTRUCTION : Remplacement du "hack" de la vue Blade par un render() propre
                ->render(function (Wealth $w) {
                    $isArchived = !is_null($w->archived_at);
                    $icon = $isArchived ? 'bs.archive-fill' : 'bs.archive';
                    $class = $isArchived ? 'text-danger' : 'text-success';

                    return "<i class='{$icon} {$class}'></i>";
                })->align(TD::ALIGN_CENTER),

            TD::make('name', __('name'))
                ->sort()
                ->render(fn(Wealth $w) => optional($w)->name),

            TD::make('unit_name', __('wealth_unit'))
                ->sort()
                ->render(fn(Wealth $w) => optional($w->unit)->name),

            TD::make('wealth_type', __('wealth_type'))
                ->sort()
                ->render(fn(Wealth $w) => optional($w->wealthType)->label),

            TD::make('validity_date', __('wealth_validity_date'))
                ->sort()
                ->render(fn(Wealth $w) => optional($w)->validity_date),

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
                                // RECONSTRUCTION : Utilisation de la permission v13+
                                ->canSee(request()->user()->can('platform.quality.wealth.edit') && is_null($wealth->archived_at)),

                            Link::make(__('Duplicate'))
                                ->route('platform.quality.wealth.edit', ["wealth" => $wealth->id, "duplicate" => true])
                                ->icon('paste')
                                // RECONSTRUCTION : Utilisation de la permission v13+
                                ->canSee(request()->user()->can('platform.quality.wealth.create')),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('wealth_remove_confirmation'))
                                ->method('remove', [
                                    'id' => $wealth->id,
                                ])
                                // RECONSTRUCTION : Utilisation de la permission v13+
                                ->canSee(request()->user()->can('platform.quality.wealth.edit') && is_null($wealth->archived_at)),
                        ]);
                }),
        ];
    }
}
