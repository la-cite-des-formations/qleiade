<?php

namespace Admin\Orchid\Layouts\Wealth;

use Orchid\Screen\Layout;
use Orchid\Screen\Layouts\Listener;

class SearchListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [
        'search.keyword',
        'search.archived',
        'search.units.',
        'search.indicators.',
        'search.conformity',
        'search.wealth_type',
        'sort',
    ];

    /**
     * What screen method should be called
     * as a source for an asynchronous request.
     *
     * The name of the method must
     * begin with the prefix "async"
     *
     * @var string
     */
    protected $asyncMethod = 'asyncFilterList';

    /**
     * @return Layout[]
     */
    protected function layouts(): iterable
    {
        return [
            ListLayout::class
        ];
    }
}
