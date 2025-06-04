<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        event(new NewNotification($message, $userId));

        $user=Auth::user();

        activity()
            ->causedBy($user)
            ->useLog('notification')
            ->withProperties([
                'user_id' => $userId,
                'message' => $message,
            ])
            ->log('Notification sent');
        return $this->respondOk(null, 'Notification sent successfully');
    }
}
