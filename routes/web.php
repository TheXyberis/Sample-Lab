<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SampleWebController;
use App\Http\Controllers\SampleWizardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;

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

    Route::get('samples/import', fn()=>view('samples.import'))->name('samples.import')->middleware('permission:samples:create');
    Route::get('samples/create-wizard', [SampleWizardController::class, 'showForm'])->name('samples.create-wizard')->middleware('permission:samples:create');
    Route::post('samples/validate-step/{step}', [SampleWizardController::class, 'validateStep'])->name('samples.validate-step')->middleware('permission:samples:create');
    Route::post('samples/store-wizard', [SampleWizardController::class, 'storeFromWizard'])->name('samples.store-wizard')->middleware('permission:samples:create');
    Route::resource('samples', SampleWebController::class);
    
    Route::get('samples/{id}/label', [SampleWebController::class, 'generateLabel'])->name('samples.label');
    Route::get('samples/{id}/barcode', [SampleWebController::class, 'downloadBarcode'])->name('samples.barcode');

    Route::resource('measurements', MeasurementController::class)->only(['index','create','edit','show','store','update'])->middleware('permission:measurements:read');
    
    Route::patch('/measurements/{id}/start', [MeasurementController::class, 'start'])->name('measurements.start')->middleware('permission:measurements:start');
    Route::patch('/measurements/{id}/finish', [MeasurementController::class, 'finish'])->name('measurements.finish')->middleware('permission:measurements:finish');
    Route::get('qc/queue', [\App\Http\Controllers\ResultController::class, 'qcQueue'])->name('qc.queue')->middleware('permission:results:review');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sample/{id}/preview', [ReportController::class, 'previewSampleReport'])->name('reports.sample.preview');
    Route::get('reports/sample/{id}/generate', [ReportController::class, 'generateSampleReport'])->name('reports.sample.generate');
    Route::get('reports/project/{id}/preview', [ReportController::class, 'previewProjectReport'])->name('reports.project.preview');
    Route::get('reports/project/{id}/generate', [ReportController::class, 'generateProjectReport'])->name('reports.project.generate');
    Route::get('users', fn()=>view('users.index'))->name('users.index')->middleware('permission:users:manage');
    Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('audit/{id}', [AuditController::class, 'show'])->name('audit.show');

    Route::prefix('methods')->group(function () {
        Route::get('/list', [MethodController::class, 'indexView'])->name('methods.index')->middleware('permission:methods:read');
        Route::get('/create', [MethodController::class, 'createView'])->name('methods.create')->middleware('permission:methods:create');
        Route::get('/{id}', [MethodController::class, 'showView'])->name('methods.show')->middleware('permission:methods:read');
        Route::get('/{id}/edit', [MethodController::class, 'editView'])->name('methods.edit')->middleware('permission:methods:update');

        Route::post('/', [MethodController::class, 'store'])->name('methods.store')->middleware('permission:methods:create');
        Route::put('/{id}', [MethodController::class, 'update'])->name('methods.update')->middleware('permission:methods:update');

        Route::post('/{id}/publish', [MethodController::class, 'publish'])->name('methods.publish')->middleware('permission:methods:publish');
        Route::post('/{id}/version', [MethodController::class, 'version'])->name('methods.version')->middleware('permission:methods:version');
    });

    Route::prefix('measurements')->group(function () {
        Route::patch('/{id}/start', [MeasurementController::class, 'start'])->name('measurements.start')->middleware('permission:measurements:start');
        Route::patch('/{id}/finish', [MeasurementController::class, 'finish'])->name('measurements.finish')->middleware('permission:measurements:finish');

        Route::get('/{id}/results', [ResultController::class, 'webIndex'])->name('results.page')->middleware('permission:results:read');
        Route::put('/{id}/results', [ResultController::class, 'saveDraft'])->name('results.saveDraft')->middleware('permission:results:edit');
        Route::post('/{id}/results/submit', [ResultController::class, 'submit'])->name('results.submit')->middleware('permission:results:submit');
        Route::post('/{id}/results/review', [ResultController::class, 'review'])->name('results.review')->middleware('permission:results:review');
        Route::post('/{id}/results/approve', [ResultController::class, 'approve'])->name('results.approve')->middleware('permission:results:approve');
        Route::post('/{id}/results/lock', [ResultController::class, 'lock'])->name('results.lock')->middleware('permission:results:lock');
        Route::post('/{id}/results/unlock', [ResultController::class, 'unlock'])->name('results.unlock')->middleware('permission:results:unlock');
    });

    Route::post('/samples/import/preview', [SampleWebController::class, 'importPreview'])->name('samples.import.preview')->middleware('permission:samples:import');
    Route::post('/samples/import/confirm', [SampleWebController::class, 'importConfirm'])->name('samples.import.confirm')->middleware('permission:samples:import');
});