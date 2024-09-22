<?php

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Broadcast::channel('chat.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    return ChatRoom::where('id', $roomId)->whereHas('users', function ($query) use ($user) {
        $query->where('users.id', $user->id);
    })->exists();
});
