<?php

namespace Database\Seeders;

use File;
use Illuminate\Database\Seeder;

class PopulateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qleiade_data_path = storage_path() . '/app/qleiade_data_prod';

        // les fichiers doivent être joué dans cet ordre (contraintes sql )
        $files = [
            '/qleiade_table_quality_label.sql',
            '/qleiade_table_criteria.sql',
            '/qleiade_table_indicator.sql',
            '/qleiade_table_unit.sql',
            '/qleiade_table_stage.sql',
            '/qleiade_table_tag.sql',
            '/qleiade_table_action.sql',
            '/qleiade_table_roles.sql',
            '/qleiade_table_users.sql',
            '/qleiade_table_role_users.sql',
            '/qleiade_table_files.sql',
            '/qleiade_table_wealth_type.sql',
            '/qleiade_table_wealth.sql',
            '/qleiade_table_wealths_actions.sql',
            '/qleiade_table_wealths_tags.sql',
            '/qleiade_table_wealths_files.sql',
            '/qleiade_table_wealths_indicators.sql',
        ];

        foreach ($files as $file) {
            $path = $qleiade_data_path . $file;
            if (file_exists($path)) {
                $res = \DB::unprepared(
                    file_get_contents($path)
                );
                if (!$res) {
                    $this->command->error($file . ' : not inserted');
                } else {
                    $this->command->info($file . " : OK");
                }
            } else {
                $this->command->error($file . ' : not exist');
            }
        }
    }
}
