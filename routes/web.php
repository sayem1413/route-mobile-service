<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsCallbackController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/sms/callback/route-mobile', [SmsCallbackController::class, 'routeMobile']);