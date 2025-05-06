<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Models\Unit;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\DriveManagement;
use Illuminate\Support\Str;

//DOC: artisan Cmd for project:init_storage
class InitStorage extends Command
{
    use DriveManagement;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:init_directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initalize directories structure according to the unit in db';

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
        if ($this->confirm('Do you wish to delete existing directories? [yes|no]', true)) {
            $count = 0;
            foreach (Storage::cloud()->allDirectories() as $dir) {
                Storage::cloud()->deleteDirectory($dir);
                $count++;
            }

            $this->info($count . ' directories are deleted');
        }
        foreach (Unit::all() as $proc) {
            $name = $this->formatDirName($proc->label);
            Storage::cloud()->makeDirectory($name);
            $this->info($name . ' are created');
        }

        if ($this->confirm('Do you wish to generate archive directory? [yes|no]', true)) {
            Storage::cloud()->makeDirectory('archive');
            $archId = $this->getDirectoryId('archive');
            foreach (Unit::all() as $proc) {
                $name = $this->formatDirName($proc->label);
                Storage::cloud()->makeDirectory($archId . "/" . $name);
                $this->info($name . ' are created');
            }
        }
        return 0;
    }
}
