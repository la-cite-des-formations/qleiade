<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Models\Indicator;
use App\Http\Traits\CSVSeeder;
use \Illuminate\Support\Facades\DB;

class IndicatorSeeder extends Seeder
{
    use CSVSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = storage_path() . '/app/indicateurs-qualiopi-seeder.csv';
        // dd($csvFile);
        $data = $this->csv_to_array($csvFile, ";");
        // DB::table('indicator')->truncate();
        DB::table('indicator')->insert($data);
    }
}
