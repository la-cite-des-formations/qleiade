<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirection vers home par défaut
Route::get('/', function () {
    return redirect("/home");
});

// Route pivot pour l'authentification
Route::get('/login', \App\Filament\Admin\Pages\Auth\Login::class)->name('login');

Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// Le catch-all de React doit EXCLURE expressément /login, /admin, /fortify et /livewire
Route::view('/{path?}/{label?}/{action?}/{action2?}{params?}', "app")
    ->where('path', '^(?!(admin|login|fortify|livewire)).*$')
    ->name('react.app');
