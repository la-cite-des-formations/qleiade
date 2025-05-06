<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Models\Stage;

class StageSeeder extends Seeder
{
    protected $data = [
        [
            'name' => 'before',
            'label' =>'Avant',
            'description' => "avant l'arrivé de l'apprenant"
        ],
        [
            'name' => 'during',
            'label' =>'Pendant',
            'description' => 'Pendant la formation'
        ],
        [
            'name' => 'after',
            'label' =>'Après',
            'description' => 'Après la formation'
        ],

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $values) {
            Stage::create($values);
        }
    }
}
