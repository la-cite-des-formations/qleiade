<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect("/home");
});

Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// On ajoute ->where(...) pour interdire à cette route de capturer les URLs commençant par 'admin' ou 'console'
Route::view('/{path?}/{label?}/{action?}/{action2?}{params?}', "app")
    ->where('path', '^(?!admin).*$')
    ->name('react.app');
