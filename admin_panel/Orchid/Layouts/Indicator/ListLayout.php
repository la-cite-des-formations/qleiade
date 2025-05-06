<?php

namespace Admin\Orchid\Layouts\Indicator;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Models\Indicator;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;

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
    protected $target = 'qualityLabel.indicators';

    /**
     * @return bool
     */
    protected function striped(): bool
    {
        return true;
    }

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make(__('criteria_number'))
                ->render(function (Indicator $indicator) {

                    return $indicator->criteria->order;
                }),
            TD::make(__('indicator_number'))
                ->render(function (Indicator $indicator) {
                    return $indicator->number;
                }),
            TD::make(__('label'))
                ->sort()
                ->render(function (Indicator $indicator) {
                    if ($this->isQualityLabelEditForm()) {
                        //in quality label edit screen
                        $view = ModalToggle::make($indicator->label)
                            ->modal('indicatorModal')
                            ->method('updateIndicator')
                            ->asyncParameters([
                                'indicator' => $indicator
                            ]);
                    } else {
                        //in indicators list screen
                        $view = Link::make($indicator->label)
                            ->route('platform.quality.quality_label.indicator.edit', ['indicator' => $indicator, 'quality_label' => $indicator->qualityLabel]);
                    }

                    return  $view;
                }),
        ];
    }

    protected function isQualityLabelEditForm()
    {
        return !is_null($this->query->get('qualityLabel'));
    }
}
