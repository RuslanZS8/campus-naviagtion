<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Welcome Page ───
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ─── Main Campus Navigation ───
Route::get('/map', [CampusController::class, 'index'])->name('home');

// ─── Language Switching Route ───
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'tr'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');
