<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouteMobileSMSController;

Route::prefix('route-mobile')
    ->controller(RouteMobileSMSController::class)
    ->group(function () {
        Route::post('/bulk-sms-bd/send', 'sendBulkSmsBd');
    });

