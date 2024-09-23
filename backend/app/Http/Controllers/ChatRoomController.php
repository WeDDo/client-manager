<?php

namespace App\Http\Controllers;

use App\DataTables\ChatRooms\ChatRoomDataTable;
use App\Http\Requests\ChatRoomRequest;
use App\Models\ChatRoom;
use App\Services\ChatRoomService;
use Illuminate\Http\JsonResponse;

class ChatRoomController extends Controller
{
    public function __construct(private ChatRoomService $chatRoomService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new ChatRoomDataTable())->get());
    }

    public function store(ChatRoomRequest $request): JsonResponse
    {
        $chatRoom = $this->chatRoomService->store($request->validated());

        return response()->json([
            'item' => $chatRoom
        ]);
    }

    public function joinChatRoom(ChatRoom $chatRoom): JsonResponse
    {
        $this->chatRoomService->joinChatRoom($chatRoom);

        return response()->json([
            'message' => 'Success!',
        ]);
    }

    public function leaveChatRoom(ChatRoom $chatRoom): JsonResponse
    {
        // TODO implement

        return response()->json([
            'message' => 'Success!',
        ]);
    }
}
