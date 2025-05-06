<?php

namespace Admin\Orchid\Layouts\Criteria;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Models\Criteria;
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
    protected $target = 'qualityLabel.criterias';

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
            TD::make(__('criteria_order'))
                ->render(function (Criteria $criteria) {

                    return $criteria->order;
                }),

            TD::make(__('label'))
                ->sort()
                ->render(function (Criteria $criteria) {
                    if ($this->isQualityLabelEditForm()) {
                        //in quality label edit screen
                        $view = ModalToggle::make($criteria->label)
                            ->modal('criteriaModal')
                            ->method('updateCriteria')
                            ->asyncParameters([
                                'criteria' => $criteria
                            ]);
                    } else {
                        //in criterias list screen
                        $view = Link::make($criteria->label)
                            ->route('platform.quality.quality_label.criteria.edit', ['criteria' => $criteria, 'quality_label' => $criteria->qualityLabel]);
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
