<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect("/home");
});


// for f5 in front routing
Route::view('/{path?}/{label?}/{action?}/{action2?}{params?}', "app");


Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::middleware(['auth'])->group(function () {
    //les images du front stockées dans public/images/nomdumodel ou nomdumodel = quality_label pour les labels qualités
    // Route::get('/storage/images/{type}/{filename}/{format?}', function (string $type, string $filename, string $format = "") {

    //     $path = '/' . $type . '/' . $filename;

    //     if (!Storage::disk('images')->exists($path)) {
    //         abort(404, "file doesn't exist");
    //     }
    //     $img = Storage::disk('images')->get($path);
    //     $image = Image::make($img);
    //     switch ($format) {
    //         case 'card':
    //             // $image->resize(140, 80)->encode('jpg', 100);
    //             break;
    //         case 'thumbnail':
    //             // $image->circle();
    //             break;
    //         default:
    //             # code...
    //             break;
    //     }
    //     $mime = $image->mime();

    //     return $image->response($mime);
    //     // return Storage::disk('public')->response($path);
    // })->where(['file' => '\w[0-9a-zA-Z-_.]+'])->name('public.images.download');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
});
