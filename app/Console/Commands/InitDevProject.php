<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Http\Traits\Meilisearchable;

class InitDevProject extends Command
{
    use Meilisearchable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'initialize mariaDb and Meilisearch with dataset and indexes';

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
        Artisan::call('optimize:clear');

        if ($this->confirm('Do you wish to refresh mariaDb ?', true)) {

            //update schema and delete all records
            Artisan::call('migrate:fresh');
            //message
            $this->info("Database schema up to date !");

            if ($this->confirm('Do you have put sql files to populate ?', true)) {
                //add new dataset (backup)
                Artisan::call('db:seed', ['class' => 'PopulateSeeder']);

                $this->info("All seeded !");
            } else {
                $this->error("Command kill and put your files !");
                return COMMAND::FAILURE;
            }

            if ($this->confirm('Do you want to add toto ?', true)) {
                //add dev admin user
                Artisan::call('project:add_admin_user');

                $this->info("Toto join the dance !");
            }

            if ($this->confirm('Do you want to seed fake wealths ?', true)) {
                $howmany = $this->ask('How many wealths?', '200');
                putenv("SEED_RECORDS=" . $howmany);
                //add new dataset (backup)
                Artisan::call('db:seed', ['class' => 'WealthSeeder']);

                $this->info("All seeded !");
            } else {
                $this->error("Command kill and put your files !");
                return COMMAND::FAILURE;
            }
            //
        }
        if ($this->confirm('Do you wish to refresh Meilisearch ?', true)) {

            //delete previous wealths
            Artisan::call('scout:flush', ['model' => "Models\Wealth"]);
            $this->info("all wealth records flushed !");

            //delete previous audits
            Artisan::call('scout:flush', ['model' => "Models\Audit"]);
            $this->info("all audit records flushed !");

            //add new wealths
            Artisan::call('scout:import', ['model' => "Models\Wealth"]);
            $this->info("all wealth records imported !");

            //add new audits
            Artisan::call('scout:import', ['model' => "Models\Audit"]);
            $this->info("all audit records imported !");

            //update meilisearch index and filterable attributes
            Artisan::call('meilisearch:update_index ');
            $this->info("Index and filterable attributes updated !");

            $this->info("Meilisearch is up to date");
        }

        if ($this->confirm('Do you want to show Meilisearch info ?', false)) {
            Artisan::call('meilisearch:metrics');
            $this->info(Artisan::output());
        }

        $this->warn("!! BRAVO !! You have a new fresh db with preprod data set ");
        return COMMAND::SUCCESS;
    }
}
