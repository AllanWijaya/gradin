<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'courier'], function () {
    Route::get('/', [App\Http\Controllers\CourierController::class, 'index']);
    Route::get('/show', [App\Http\Controllers\CourierController::class, 'show']);
    Route::post('/store', [App\Http\Controllers\CourierController::class, 'store']);
    Route::delete('/destroy/{courier}', [App\Http\Controllers\CourierController::class, 'destroy']);
});
