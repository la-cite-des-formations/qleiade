<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Models\Stage;
use Models\Action;
use Models\QualityLabel;
use Models\Indicator;

//NOTE : make seeder to test suite
class TestSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \Models\User::factory(10)->create();
        $this->call(WealthTypeSeeder::class);

        // add fake quality labels and indicators
        QualityLabel::factory(2)->create()->each(function ($qualityLabel) {
            $qualityLabel->indicators()->saveMany(Indicator::factory(10)->make(['quality_label_id' => $qualityLabel->id]));
        });

        $this->call(UnitSeeder::class);

        //seed fake stages and actions
        Stage::factory(3)->create()->each(function ($stage) {
            $stage->actions()->saveMany(Action::factory(10)->make(['stage_id' => $stage->id]));
        });
    }
}
