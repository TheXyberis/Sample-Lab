<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MethodController;
use App\Http\Controllers\Api\MeasurementController;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/methods',[MethodController::class,'index']);
    Route::post('/methods',[MethodController::class,'store']);
    Route::get('/methods/{id}',[MethodController::class,'show']);
    Route::put('/methods/{id}',[MethodController::class,'update']);
    Route::post('/methods/{id}/version',[MethodController::class,'version']);
    Route::post('/methods/{id}/publish',[MethodController::class,'publish']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/measurements',[MeasurementController::class,'index']);
    Route::post('/measurements',[MeasurementController::class,'store']);
    Route::get('/measurements/{id}',[MeasurementController::class,'show']);
    Route::put('/measurements/{id}',[MeasurementController::class,'update']);
    Route::patch('/measurements/{id}/start',[MeasurementController::class,'start']);
    Route::patch('/measurements/{id}/finish',[MeasurementController::class,'finish']);
});