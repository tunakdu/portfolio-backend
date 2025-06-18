<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Ana sayfa ve frontend route'ları (API hariç)
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
