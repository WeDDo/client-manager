<?php

use App\Http\Controllers\AuthController;
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
    });
    Route::apiResource('email-settings', EmailSettingController::class);
});
