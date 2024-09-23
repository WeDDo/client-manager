<?php

namespace App\Services;

use App\Models\ChatRoom;

class ChatRoomService
{
    public function store(array $data): ChatRoom
    {
        return ChatRoom::create($data);
    }

    public function joinChatRoom(ChatRoom $chatRoom): void
    {
        $chatRoom->users()->attach(auth()->user()->id);
    }

    public function leaveChatRoom(ChatRoom $chatRoom): void
    {
        $chatRoom->users()->detach(auth()->user()->id);
    }
}


