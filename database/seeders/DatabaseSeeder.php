<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    //DOC: All seeders are for production
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //DOC: 3 types added (file, ypareo, link) with array in seeder
        $this->call(WealthTypeSeeder::class);

        //DOC: 1 label added (Qualiopi)
        $this->call(QualityLabelSeeder::class);

        //DOC: CFA unit with array in seeder
        $this->call(UnitSeeder::class);

        // $this->call(TagSeeder::class);

        //DOC: Qualiopi indicators with csv file
        $this->call(IndicatorSeeder::class);

        //DOC: 3 stages added (avant, pendant, après) with array in seeder
        $this->call(StageSeeder::class);

        //DOC: All actions with csv file
        $this->call(ActionSeeder::class);
    }
}
