<?php

namespace App\Http\Traits;

use Http\Client\Common\Exception\ServerErrorException;
use MeiliSearch\Client;
use voku\helper\StopWords;


trait Meilisearchable
{
    protected function makeFilters($index, $filters)
    {
        $config = config('scout.meilisearch.filtrable_attributes');
        $availableFilters = $config[$index];
        $filter = "";
        $and = " AND ";

        if ($filters) {
            foreach ($filters as $key => $value) {
                if (in_array($key, $availableFilters) && !is_null($value)) {
                    if (is_int($value)) {
                        $quote = "";
                    } else {
                        $quote = "'";
                    }
                    $filter .= $key . "=" . $quote . $value . $quote . $and;
                } else {
                }
            }
            $filter = rtrim($filter, $and);
        }
        return $filter;
    }

    protected function connectToMeilisearch()
    {
        try {
            $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        } catch (\MeiliSearch\Exceptions\CommunicationException $th) {
            abort(500);
        }
        return $client;
    }

    protected function updateSortableAttributes(Client $client): void
    {
        // found in meilisearch config file
        // 'sortable_attributes' => [
        //     'wealths' => ['unit'],
        // ],
        $config = config('scout.meilisearch.sortable_attributes');
        foreach ($config as $index => $sorters) {
            $res = $client->index($index)->updateSortableAttributes($sorters);
            //write array
        }

        $this->info('Updated sortable attributes...');
    }

    protected function updateFilterableAttributes(Client $client): void
    {
        //found in meilisearch config file
        // 'filtrable_attributes' => [
        //     'wealths' => ['unit'],
        // ],
        $config = config('scout.meilisearch.filtrable_attributes');
        foreach ($config as $index => $filters) {
            $client->index($index)->updateFilterableAttributes($filters);
        }

        $this->info('Updated filterable attributes...');
    }

    protected function updateSettings(Client $client): void
    {
        $settings = config('scout.meilisearch.settings');
        foreach ($settings as $index => $setting) {
            foreach ($setting as $key => $value) {
                switch ($key) {
                    case 'stop_words_languages':
                        # code...
                        $stopWords = new StopWords();

                        $client->index($index)->updateSettings([
                            'stopWords' => $stopWords->getStopWordsFromLanguage($value)
                        ]);

                        $this->info('Updated settings ...');

                        break;
                    case 'maxTotalHits':
                        $client->index($index)->updatePagination([
                            $key => $value
                        ]);

                        $this->info('Updated settings ...');
                        break;

                    default:
                        $this->info('No settings to update...');
                        break;
                }
            }
        }
    }

    protected function getStats(Client $client)
    {
        return $client->stats();
    }

    protected function getHealth(Client $client)
    {
        return $client->stats();
    }

    protected function getIndexes(Client $client)
    {
        return $client->getAllIndexes();
    }

    protected function getMetrics(Client $client)
    {
        $status = $this->getHealth($client);
        $indexes = $this->getIndexes($client);

        $response["status"] = ["databaseSize" => $status["databaseSize"], "lastUpdate" => $status['lastUpdate']];
        $response["total_indexes"] = $indexes->getTotal();

        foreach ($indexes->getResults() as $index) {

            $ind = $index->getUid();
            $settings = $client->index($ind)->getSettings();
            $stats = $client->index($ind)->stats();

            $response["indexes"][$ind] = [
                "stopwords" => count($settings['stopWords']),
                "filterableAttributes" => count($settings['filterableAttributes']),
                "sortableAttributes" => count($settings['sortableAttributes']),
                "stats" => $stats
            ];
        }
        return $response;
    }
}
