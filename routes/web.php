<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SampleWebController;
use App\Http\Controllers\SampleWizardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn()=>view('dashboard'))->name('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/admin-only', function () {
        return "OK ADMIN";
    })->middleware('role:Admin');

    Route::get('samples/import', fn()=>view('samples.import'))->name('samples.import');
    Route::get('samples/create-wizard', [SampleWizardController::class, 'showForm'])->name('samples.create-wizard');
    Route::post('samples/validate-step/{step}', [SampleWizardController::class, 'validateStep'])->name('samples.validate-step');
    Route::resource('samples', SampleWebController::class);

    Route::resource('measurements', MeasurementController::class)->only(['index','create','edit','show','store','update']);

    Route::get('qc/queue', fn()=>view('qc.queue'))->name('qc.queue');
    Route::get('reports', fn()=>view('reports.index'))->name('reports.index');
    Route::get('users', fn()=>view('users.index'))->name('users.index');
    Route::get('audit', fn()=>view('audit.index'))->name('audit.index');

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

    Route::post('/samples/import/preview', [App\Http\Controllers\Api\SampleController::class, 'importPreview'])->name('samples.import.preview');
    Route::post('/samples/import/confirm', [App\Http\Controllers\Api\SampleController::class, 'importConfirm'])->name('samples.import.confirm');
});