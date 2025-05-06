<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Models\Action;
use App\Http\Traits\CSVSeeder;

class ActionSeeder extends Seeder
{
    use CSVSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = storage_path() . '/app/actions-seeder.csv';
        // dd($csvFile);
        $data = $this->csv_to_array($csvFile, ";");
        DB::table('action')->insert($data);
    }
}
