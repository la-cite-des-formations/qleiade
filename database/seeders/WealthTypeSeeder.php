<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Models\WealthType;

class WealthTypeSeeder extends Seeder
{
    protected $data = [
        [
            'name' => 'file',
            'label' => 'fichier',
            'description' => 'the ressource is a file'
        ],
        [
            'name' => 'link',
            'label' => 'lien',
            'description' => 'the ressource is a link'
        ],
        [
            'name' => 'ypareo',
            'label' => 'ypareo',
            'description' => 'the ressource is an ypareo process'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $values) {
            WealthType::create($values);
        }
    }
}
