<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Faker\Factory as Faker;
use Models\Wealth;

use School\Manager\SchoolManager;

class WealthSeeder extends Seeder
{

    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfRecords = env('SEED_RECORDS', 200);

        $validityDate = now()->format('d-m-Y');

        $periods = Cache::remember('periods_seeder', now()->addMinute(10), function () use ($validityDate) {
            return app(SchoolManager::class)->getPeriods(null, $validityDate, true);
        });
        //on s'en sert dans la factory
        $groups = Cache::remember('groups_seeder', now()->addMinute(10), function () use ($periods) {
            return json_decode(app(SchoolManager::class)->getGroups($periods, [])->toJson())->data;
        });

        Wealth::factory($numberOfRecords)
            ->create();
    }
}
