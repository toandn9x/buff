<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfParserController;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [PdfParserController::class, 'test']);
Route::get('/sign', [PdfParserController::class, 'sign']);
Route::post('/sign-pdf', [PdfParserController::class, 'signPdf']);
Route::get('/aicevn', [PdfParserController::class, 'aicevn']);
Route::post('/paicevn', [PdfParserController::class, 'paicevn']);