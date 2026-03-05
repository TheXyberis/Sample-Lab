<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;

Route::get('login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('login', [WebAuthController::class, 'login'])->name('login.submit');
Route::get('/dashboard', function(){
    return view('dashboard');
})->middleware('auth');