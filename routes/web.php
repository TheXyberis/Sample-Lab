<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\MeasurementController;

Route::get('login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('login', [WebAuthController::class, 'login'])->name('login.submit');

Route::get('/dashboard', function(){
    return view('dashboard');
})->middleware('auth');

Route::get('/admin-only', function () {
    return "OK ADMIN";
})->middleware(['auth','role:Admin']);

Route::middleware('auth')->group(function(){
    Route::get('/methods/list', [MethodController::class,'indexView'])->name('methods.index');

    Route::get('/methods/create', [MethodController::class,'createView'])->name('methods.create');
    Route::get('/methods/{id}/edit', [MethodController::class,'editView'])->name('methods.edit');
    Route::get('/methods/{id}', [MethodController::class,'showView'])->name('methods.show');

    Route::post('/methods', [MethodController::class, 'store'])->name('methods.store');
    Route::put('/methods/{id}', [MethodController::class, 'update'])->name('methods.update');

    Route::post('/methods/{id}/publish', [MethodController::class,'publish'])->name('methods.publish');
});

Route::middleware('auth')->group(function(){
    Route::get('/measurements', [MeasurementController::class,'indexView'])->name('measurements.index');
    Route::get('/measurements/create', [MeasurementController::class,'createView'])->name('measurements.create');
    Route::get('/measurements/{id}/edit', [MeasurementController::class,'editView'])->name('measurements.edit');

    Route::patch('/measurements/{id}/start',[MeasurementController::class,'start'])->name('measurements.start');
    Route::patch('/measurements/{id}/finish',[MeasurementController::class,'finish'])->name('measurements.finish');
});