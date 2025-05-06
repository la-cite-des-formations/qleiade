<?php

namespace Admin\Orchid\Fields;

use Orchid\Screen\Field;

class SearchField extends Field
{
    /**
     * Blade template
     *
     * @var string
     */
    protected $view = 'partials.search';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'name',
        'type',
        'class',
        'value',
        'placeholder',
        'id',
    ];
}
