<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use Models\Unit;
use App\Http\Traits\DriveManagement;

class SyncDirectories extends Command
{
    use DriveManagement;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:sync_directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync directories and unit in db';

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
        foreach (Unit::all() as $proc) {
            $name = $this->formatDirName($proc->label);
            $ex = $this->getDirectoryId($name);
            if ($ex == false) {
                $this->info($name . " doesn't exist. ");
                Storage::cloud()->makeDirectory($name);
                $this->info($name . ' are created');
            } else {
                $this->info($name . " exist. ");
            }
        }

        return $this->info('all directories are synchronized !!');
    }
}
