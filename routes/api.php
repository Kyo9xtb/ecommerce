<?php

use App\Http\Controllers\Api\V1\Account\AccountController;
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

    Route::prefix('/customer/auth')->group(function () {
        Route::post('/login', [AccountController::class, 'CustomerAuth']);
        Route::post('/logout', [AccountController::class, 'CustomerAuth'])->middleware('jwt.customer');
        Route::post('/me', [AccountController::class, 'CustomerAuth'])->middleware('jwt.customer');
    });

    Route::match(['get', 'post', 'put', 'delete'], '/customer', [ApiController::class, 'Customer']);
    Route::get('/tour/{slug}', [ApiController::class, 'tour']);
    Route::match(['get', 'post', 'put', 'delete'], '/tour', [ApiController::class, 'tour']);
    Route::match(['get', 'post', 'put', 'delete'], '/tour-require', [ApiController::class, 'TourRequire']);
    Route::match(['get', 'post', 'put', 'delete'], '/booking-tour', [ApiController::class, 'BookingTour']);
    Route::match(['get', 'post', 'put', 'delete'], '/contact-customer', [ApiController::class, 'ContactCustomer']);
    Route::get('/news/{slug?}', [ApiController::class, 'News']);
    Route::match(['post', 'put', 'delete'], '/news', [ApiController::class, 'News']);
    Route::match(['get', 'post', 'put', 'delete'], '/cart', [ApiController::class, 'Cart']);
    Route::match(['get', 'post', 'put', 'delete'], '/passenger-information-tour', [ApiController::class, 'PassengerInformationTour']);
    Route::get('/tourist-destinations/{slug?}', [ApiController::class, 'TouristDestination']);
    Route::match(['post', 'put', 'delete'], '/tourist-destinations', [ApiController::class, 'TouristDestination']);
});
