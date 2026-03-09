<?php

use App\Jobs\ProcessPodcast;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/process-podcast', function () {
    // Отправляем задачу в очередь
    ProcessPodcast::dispatch('Новый подкаст о Laravel');

    return response()->json(['message' => 'Задача отправлена в очередь!']);
});
