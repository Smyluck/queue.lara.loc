<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HorizonDemoController;
use App\Http\Controllers\PodcastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/send-report', ReportController::class);

Route::post('/events', [EventController::class, 'sendEvent']);

// Horizon Demo Routes
Route::prefix('horizon/demo')->group(function () {
    Route::get('/info', [HorizonDemoController::class, 'info']);
    Route::post('/single-event', [HorizonDemoController::class, 'singleEvent']);
    Route::post('/batch-events', [HorizonDemoController::class, 'batchEvents']);
    Route::post('/send-reports', [HorizonDemoController::class, 'sendReports']);
    Route::post('/monitor-now', [HorizonDemoController::class, 'monitorNow']);
    Route::post('/complex-scenario', [HorizonDemoController::class, 'complexScenario']);

    Route::post('/podcasts', [PodcastController::class, 'store']);
    Route::get('/podcasts/{batchId}', [PodcastController::class, 'progress']);
});