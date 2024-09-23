<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function getChatMessages(ChatRoom $chatRoom): JsonResponse
    {
        $chatRoom->load(['users', 'messages.senderUser']);

        return response()->json([
            'chat_messages' => $chatRoom->messages()
                ->with(['senderUser'])
                ->orderBy('id')
                ->get(),
            'chat_users' => $chatRoom->users,
        ]);
    }

    public function sendChatMessageToChatRoom(Request $request, ChatRoom $chatRoom): JsonResponse
    {
        $chatMessage = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_user_id' => auth()->user()->id,
            'message' => request()->input('message')
        ]);

        broadcast(new SendMessage($chatMessage));

        return response()->json([
            'item' => $chatMessage
        ]);
    }
}
