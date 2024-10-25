<?php

namespace App\Services;

use App\Models\ChatRoom;

class ChatRoomService
{
    public function store(array $data): ChatRoom
    {
        $chatRoom = ChatRoom::create($data);
        $chatRoom->users()->attach(auth()->user()->id);

        return $chatRoom;
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


