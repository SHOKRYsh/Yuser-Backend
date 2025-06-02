<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Events\NewMessage;
use Modules\Chat\Models\Chat;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string',
        ]);
        $message = Chat::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
        ]);

        event(new NewMessage(
            $request->input('message'),
            $request->input('sender_id'),
            $request->input('receiver_id')
        ));

        return $this->respondOk(null, 'Message sent successfully');
    }

    public function getMessages($senderId, $receiverId)
    {
        $messages = Chat::with(['sender:id,name,profile_image', 'receiver:id,name,profile_image'])
            ->where(function($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $senderId)
                    ->where('receiver_id', $receiverId);
            })
            ->orWhere(function($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $receiverId)
                    ->where('receiver_id', $senderId);
            })
            ->orderBy('created_at', 'asc')
            ->paginate();

        return $this->respondOk($messages);
    }
}
