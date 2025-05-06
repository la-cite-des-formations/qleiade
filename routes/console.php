<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//DOC: artisan command project:fresh_db
Artisan::command('project:fresh_db', function () {
    Artisan::call('migrate:fresh');
    // Artisan::call('orchid:admin', ['name'=>env('ORCHID_USER_ADMIN_NAME'), 'email'=>env('ORCHID_USER_ADMIN_MAIL'), 'password'=>env('ORCHID_USER_ADMIN_PASSWORD')]);
})->describe('fresh db seeded ready for dev env');

Artisan::command('project:add_admin_user', function () {
    Artisan::call('orchid:admin', ['name' => env('ORCHID_USER_ADMIN_NAME'), 'email' => env('ORCHID_USER_ADMIN_MAIL'), 'password' => env('ORCHID_USER_ADMIN_PASSWORD')]);
})->describe('add admin user ready for dev env');
