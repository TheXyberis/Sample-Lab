<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SampleWebController;
use App\Http\Controllers\SampleWizardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
        
        $middleware->validateCsrfTokens([
            '/samples/import/preview',
            '/samples/import/confirm',
            '/measurements/bulk-plan',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
