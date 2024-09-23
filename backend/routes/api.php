<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\EmailInboxSettingController;
use App\Http\Controllers\EmailMessageController;
use App\Http\Controllers\EmailSettingController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('registration', [AuthController::class, 'registration']);

Route::get('sanctum/csrf-cookie', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('attachments')->group(function () {
        Route::prefix('{attachment}')->group(function () {
            Route::get('download', [AttachmentController::class, 'downloadAttachment']);
        });
    });
    Route::apiResource('attachments', AttachmentController::class);

    Route::prefix('users')->group(function () {
        Route::prefix('{user}')->group(function () {
            Route::prefix('chat')->group(function () {
                Route::get('get-chat-messages', [ChatMessageController::class, 'getChatMessages']);
                Route::post('send-chat-message-to-user', [ChatMessageController::class, 'sendChatMessageToUser']);
            });
        });
    });

    Route::prefix('chat-rooms')->group(function () {
        Route::prefix('{chatRoom}')->group(function () {
            Route::get('join', [ChatRoomController::class, 'joinChatRoom']);
            Route::get('leave', [ChatRoomController::class, 'leaveChatRoom']);

            Route::prefix('chat')->group(function () {
                Route::get('get-chat-messages', [ChatMessageController::class, 'getChatMessages']);
                Route::post('send-chat-message-to-chat-room', [ChatMessageController::class, 'sendChatMessageToChatRoom']);
            });
        });
    });
    Route::apiResource('chat-rooms', ChatRoomController::class);

    Route::prefix('email-settings')->group(function () {
        Route::prefix('{emailSetting}')->group(function () {
            Route::get('copy', [EmailSettingController::class, 'copy']);
            Route::get('check-connection', [EmailSettingController::class, 'checkConnection']);
        });
    });
    Route::apiResource('email-settings', EmailSettingController::class);

    Route::prefix('email-messages')->group(function () {
        Route::get('get-additional-data', [EmailMessageController::class, 'getAdditionalData']);
        Route::get('get-emails-using-imap', [EmailMessageController::class, 'getEmailsUsingImap']);

        Route::prefix('{emailMessage}')->group(function () {
            Route::post('send', [EmailMessageController::class, 'replyToEmailUsingSmtp']);

        });
    });
    Route::apiResource('email-messages', EmailMessageController::class);

    Route::apiResource('email-inbox-settings', EmailInboxSettingController::class);
});
