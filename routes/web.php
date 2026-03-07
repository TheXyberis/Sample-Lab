<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SampleWebController;

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/admin-only', function () {
        return "OK ADMIN";
    })->middleware('role:Admin');

    Route::prefix('methods')->group(function () {
        Route::get('/list', [MethodController::class, 'indexView'])->name('methods.index');
        Route::get('/create', [MethodController::class, 'createView'])->name('methods.create');
        Route::get('/{id}', [MethodController::class, 'showView'])->name('methods.show');
        Route::get('/{id}/edit', [MethodController::class, 'editView'])->name('methods.edit');

        Route::post('/', [MethodController::class, 'store'])->name('methods.store');
        Route::put('/{id}', [MethodController::class, 'update'])->name('methods.update');

        Route::post('/{id}/publish', [MethodController::class, 'publish'])->name('methods.publish');
    });

    Route::prefix('measurements')->group(function () {
        Route::get('/', [MeasurementController::class, 'indexView'])->name('measurements.index');
        Route::get('/create', [MeasurementController::class, 'createView'])->name('measurements.create');
        Route::get('/{id}/edit', [MeasurementController::class, 'editView'])->name('measurements.edit');

        Route::patch('/{id}/start', [MeasurementController::class, 'start'])->name('measurements.start');
        Route::patch('/{id}/finish', [MeasurementController::class, 'finish'])->name('measurements.finish');

        Route::get('/{id}/results', [ResultController::class, 'webIndex'])->name('results.page');

        Route::put('/{id}/results', [ResultController::class, 'saveDraft'])->name('results.saveDraft');
        Route::post('/{id}/results/submit', [ResultController::class, 'submit'])->name('results.submit');
        Route::post('/{id}/results/review', [ResultController::class, 'review'])->name('results.review');
        Route::post('/{id}/results/approve', [ResultController::class, 'approve'])->name('results.approve');
        Route::post('/{id}/results/lock', [ResultController::class, 'lock'])->name('results.lock');
        Route::post('/{id}/results/unlock', [ResultController::class, 'unlock'])->name('results.unlock');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/samples/import', function() {
        return view('samples.import');
    })->name('samples.import');

    Route::post('/samples/import/preview', [App\Http\Controllers\Api\SampleController::class, 'importPreview'])->name('samples.import.preview');
    Route::post('/samples/import/confirm', [App\Http\Controllers\Api\SampleController::class, 'importConfirm'])->name('samples.import.confirm');
});

