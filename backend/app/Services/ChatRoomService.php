<?php

namespace App\Services;

use App\Models\ChatRoom;

class ChatRoomService
{
    public function store(array $data): ChatRoom
    {
        return ChatRoom::create($data);
    }
}


