<?php

namespace Admin\Orchid\Layouts\Action;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use Admin\Orchid\Filters\StagesFilter;

class FiltersLayout extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            StagesFilter::class,
        ];
    }
}
