<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/send-report', ReportController::class);

Route::post('/events', [EventController::class, 'sendEvent']);
