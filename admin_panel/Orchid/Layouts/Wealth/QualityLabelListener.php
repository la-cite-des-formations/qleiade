<?php

namespace Admin\Orchid\Layouts\Wealth;

use Illuminate\Support\Arr;

use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Sight;

use Models\Indicator;

class QualityLabelListener extends Listener
{

    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = ['wealth.qualityLabel', 'wealth.id'];

    /**
     * What screen method should be called
     * as a source for an asynchronous request.
     *
     * The name of the method must
     * begin with the prefix "async"
     *
     * @var string
     */
    protected $asyncMethod = 'asyncQualityLabel';

    /**
     * @return Layout[]
     */
    protected function layouts(): iterable
    {
        $options = [];
        if (isset($this->query['qualityLabel'])) {
            $qualityLabelId = $this->query['qualityLabel'];
            $o = Indicator::with(['qualityLabel'])->whereHas('qualityLabel', function ($query) use ($qualityLabelId) {
                $query->where('quality_label_id', '=', $qualityLabelId);
            })->orderBy('number')->get();
            // Utilisez la méthode reduce pour construire le tableau associatif
            $options = $o->reduce(function ($carry, $item) {
                $carry[$item->id] = $item->label;
                // $carry[$item->id] = $item->number . ' : ' . $item->label;
                return $carry;
            }, []);
        }

        return [
            Layout::rows(
                [
                    Select::make('wealth.indicators')
                        ->fromModel(Indicator::class, 'label', 'id')
                        ->multiple()
                        ->options($options)
                        ->title(__('indicator_select_title'))
                        ->require()
                ]
            )->canSee(count($options) > 0),
            Layout::legend('empty wealth label', [
                Sight::make('Avertissement')->render(function () {
                    return __('empty_indicators_for_label_card',);
                })
            ])
                ->canSee(count($options) == 0)
        ];
    }
}
