<?php

use App\Jobs\MonitorQueueLoad;

$schedule->job(new MonitorQueueLoad())
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

$schedule->command('queue:generate-load 200 --type=mixed')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onOneServer();

$schedule->command('horizon:snapshot')->everyFiveMinutes();
