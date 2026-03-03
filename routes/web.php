<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirection vers home par défaut (sera gérée par le middleware auth)
Route::get('/', function () {
    return redirect("/home");
});

// Route 'login' nommée pour les besoins de Laravel, redirigeant vers le panel Filament
Route::get('/login', fn() => redirect('/auth/login'))->name('login');

// Routes pour l'authentification Google via Socialite (version Web/Filament)
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// Route publique pour afficher la 403
Route::get('/access-denied', function () {
    abort(403);
});

// Le catch-all de React doit EXCLURE expressément /login, /admin, /fortify et /livewire
Route::view('/{path?}/{label?}/{action?}/{action2?}{params?}', "app")
    ->where('path', '^(?!(admin|login|fortify|livewire)).*$')
    ->name('react.app');
