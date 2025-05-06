<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use voku\helper\StopWords;
use App\Http\Traits\Meilisearchable;

class UpdateMeilisearchIndex extends Command
{
    use Meilisearchable;

    //variable de configuration dans le fichier config/meilisearch
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:update_index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Meilisearch\'s index and filterable attributes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = $this->connectToMeilisearch();

        $this->updateSortableAttributes($client);

        $this->updateFilterableAttributes($client);
        $this->updateSettings($client);

        return Command::SUCCESS;
    }

}
