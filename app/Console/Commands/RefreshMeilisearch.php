<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshMeilisearch extends Command
{
    protected $signature = 'meilisearch:refresh_all {model}';
    protected $description = 'Flush, import and update Meilisearch index settings for a given model';

    public function handle()
    {
        $model = $this->argument('model');

        if (!class_exists($model)) {
            $this->error("Model {$model} not found.");
            return Command::FAILURE;
        }

        $this->info("Flushing Meilisearch index for {$model}...");
        Artisan::call("scout:flush", ['model' => $model]);
        $this->line(Artisan::output());

        $this->info("Importing records into Meilisearch for {$model}...");
        Artisan::call("scout:import", ['model' => $model]);
        $this->line(Artisan::output());

        $this->info("Updating Meilisearch settings...");
        Artisan::call("meilisearch:update_index");
        $this->line(Artisan::output());

        $this->info("✅ Meilisearch index refreshed successfully for {$model}");
        return Command::SUCCESS;
    }
}
