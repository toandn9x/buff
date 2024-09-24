<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfParserController;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [PdfParserController::class, 'test']);
Route::get('/test2', [ApiController::class, 'banks']);

