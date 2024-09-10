<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailMessageController;
use App\Http\Controllers\EmailSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('registration', [AuthController::class, 'registration']);

Route::get('sanctum/csrf-cookie', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('email-settings')->group(function () {
        Route::get('{emailSetting}/copy', [EmailSettingController::class, 'copy']);
        Route::get('{emailSetting}/check-connection', [EmailSettingController::class, 'checkConnection']);
    });
    Route::apiResource('email-settings', EmailSettingController::class);

    Route::prefix('email-messages')->group(function () {
         Route::get('get-emails-using-imap', [EmailMessageController::class, 'getEmailsUsingImap']);
    });
    Route::apiResource('email-messages', EmailMessageController::class);
});
