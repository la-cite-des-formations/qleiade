<?php

namespace Admin\Orchid\Layouts\Wealth;

use Illuminate\Support\Arr;

use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

use Models\Action;

class UnitListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = ['wealth.unit', 'wealth.id'];

    /**
     * What screen method should be called
     * as a source for an asynchronous request.
     *
     * The name of the method must
     * begin with the prefix "async"
     *
     * @var string
     */
    protected $asyncMethod = 'asyncUnit';

    /**
     * @return Layout[]
     */
    protected function layouts(): iterable
    {
        $options = [];
        $unit = "";
        if (isset($this->query['unit'])) {
            $unit = $this->query['unit'];
            $o = Action::with(['unit'])->whereHas('unit', function ($query) use ($unit) {
                $query->where('unit_id', '=', $unit);
            })->orderBy('order')->get();
            // Utilisez la méthode reduce pour construire le tableau associatif
            $options = $o->reduce(function ($carry, $item) {
                $carry[$item->id] = $item->label;
                // $carry[$item->id] = $item->order . ' - ' . $item->label . ' (' . $item->stage->label . ')';
                return $carry;
            }, []);
        }
        return [
            Layout::rows(
                [
                    Select::make('wealth.actions')
                        ->fromModel(Action::class, 'label', 'id')
                        ->multiple()
                        ->options($options)
                        ->title(__('action_select_title'))
                ]
            )
                ->canSee(count($options) > 0),
            Layout::legend('empty wealth type', [
                Sight::make('Avertissement')->render(function () {
                    return __('empty_actions_for_unit_card',);
                })
            ])
                ->canSee(count($options) == 0)
        ];
    }
}
