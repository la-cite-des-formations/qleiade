<?php

return [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key' => env('MEILISEARCH_KEY', null),
    'sortable_attributes' => [
        'wealths' => ['unit', 'actions'],
    ],
    'filtrable_attributes' => [
        'wealths' => ['unit', 'actions', 'archived', 'granularity', 'granularity_type', 'granularity_id', 'validity_date', 'wealth_type'],
    ],
    'settings' => [
        'wealths' => [
            'stop_words_languages' => 'fr',
            'maxTotalHits' => 3000,
        ],
    ],

];
