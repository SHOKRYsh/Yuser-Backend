<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'message' => 'required|string',
        ]);

        $userId = $request->input('user_id');
        $message = $request->input('message');

        broadcast(new NewNotification($message, $userId));

        return $this->respondOk(null, 'Notification sent successfully');
    }
}
