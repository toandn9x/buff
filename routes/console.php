<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Route::get('/banks', [ApiController::class, 'banks']);
Route::get('/aicevn', [Controller::class, 'aicevn']);
