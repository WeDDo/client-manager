<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\EmailInboxSettingController;
use App\Http\Controllers\EmailMessageController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\PartnerContactController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('registration', [AuthController::class, 'registration']);

Route::get('sanctum/csrf-cookie', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('autocomplete/search', [AutocompleteController::class, 'search']);
    Route::post('autocomplete/search-by-id', [AutocompleteController::class, 'searchById']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::prefix('attachments')->group(function () {
        Route::prefix('{attachment}')->group(function () {
            Route::get('download', [AttachmentController::class, 'downloadAttachment']);
        });
    });
    Route::apiResource('attachments', AttachmentController::class);

    Route::prefix('data-tables')->group(function () {
        Route::post('clear-filter', [DataTableController::class, 'clearFilter']);
        Route::post('reset-columns', [DataTableController::class, 'resetColumns']);
        Route::post('update-active-columns', [DataTableController::class, 'updateActiveColumns']);
    });

    Route::prefix('users')->group(function () {
        Route::prefix('{user}')->group(function () {
            Route::prefix('chat')->group(function () {
                Route::get('get-chat-messages', [ChatMessageController::class, 'getChatMessages']);
                Route::post('send-chat-message-to-user', [ChatMessageController::class, 'sendChatMessageToUser']);
            });
        });
    });

    Route::prefix('partners')->group(function () {
        Route::prefix('{partner}')->group(function () {
            Route::get('contacts', [PartnerContactController::class, 'index']);
        });
    });
    Route::apiResource('partners', PartnerController::class);

    Route::apiResource('contacts', ContactController::class);

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

    Route::prefix('email-inbox-settings')->group(function () {
        Route::get('get-inboxes-imap', [EmailInboxSettingController::class, 'getInboxesImap']);
        Route::post('create-inboxes', [EmailInboxSettingController::class, 'createInboxes']);
    });
    Route::apiResource('email-inbox-settings', EmailInboxSettingController::class);
});
