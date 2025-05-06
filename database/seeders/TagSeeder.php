<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::factory(7)->create();
    }
}
