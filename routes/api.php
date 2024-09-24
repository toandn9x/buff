<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/banks', [ApiController::class, 'banks']);
Route::get('/getbytaxcode', [ApiController::class, 'getByTaxCode']);
Route::get('/buff', [ApiController::class, 'buff']);


Route::get('/buffv2', [ApiController::class, 'buffv2']);
