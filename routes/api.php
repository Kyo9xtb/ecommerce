<?php

use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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

Route::get('/', function () {
    echo 'E-Commerce Server';
    die();
});

Route::prefix('v1')->namespace('V1')->group(function () {
    Route::prefix('demo')->group(function () {
        Route::get('/demo1', [ApiController::class, 'demo1'])->name('.demo1'); //api/v1/demo/demo1

    });

    Route::match(['get', 'post', 'put', 'delete'], '/user', [ApiController::class, 'user']);
    Route::get('/tour/{slug}', [ApiController::class, 'tour']);
    Route::match(['get', 'post', 'put', 'delete'], '/tour', [ApiController::class, 'tour']);
});
