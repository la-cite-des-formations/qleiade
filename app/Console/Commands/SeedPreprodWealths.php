<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Http\Traits\Meilisearchable;

class SeedPreprodWealths extends Command
{
    use Meilisearchable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:seed_preprod_wealths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inject 200 wealths in pre-prod';

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
        //refresh stored config
        Artisan::call('optimize:clear');

        //seed 200 wealths
        $howmany = 200;
        putenv("SEED_RECORDS=" . $howmany);
        //add new dataset (backup)
        Artisan::call('db:seed', ['class' => 'WealthSeeder']);

        $this->info("All seeded !");
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

        $this->info("Meilisearch is up to date");

        $this->warn("!! BRAVO !! You have a new fresh db with preprod data set ");
        return COMMAND::SUCCESS;
    }
}
