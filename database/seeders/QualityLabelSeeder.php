<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Models\QualityLabel;
use Models\Indicator;

class QualityLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        QualityLabel::create([
            "name" => "qualiopi",
            "label" => "Qualiopi",
        ]);
    }
}
