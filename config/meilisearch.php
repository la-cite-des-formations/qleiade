<?php

return [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key'  => env('MEILISEARCH_KEY', null),

    'sortable_attributes' => [
        'wealths' => ['archived', 'name', 'unit_name', 'wealth_type', 'validity_date'],
    ],
    'filtrable_attributes' => [
        'wealths' => ['id', 'unit', 'indicators', 'actions', 'archived', 'granularity', 'granularity_type', 'granularity_id', 'validity_date', 'wealth_type'],
    ],

    'settings' => [
        'wealths' => [
            'stop_words_languages'   => 'fr',
            'maxTotalHits'           => 3000,
            'searchable_attributes'  => [
                'name',
                'description',
                'indicators_labels',
                'indicators_quality_labels',
                'tags_label',
            ],
        ],
    ],
];
