<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages/sender/{senderId}/receiver/{receiverId}', [ChatController::class, 'getMessages']);
});


