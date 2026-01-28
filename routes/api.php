<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouteMobileSMSController;
use App\Http\Controllers\SmsCallbackController;

Route::prefix('route-mobile')
    ->controller(RouteMobileSMSController::class)
    ->group(function () {
        Route::post('/bulk-sms-bd/send', 'sendBulkSmsBd');
    });


Route::prefix('callback')
    ->as('callback.')
    ->controller(SmsCallbackController::class)
    ->group(function () {
        Route::post('/route-mobile', 'routeMobile')->name('route-mobile');
    });

