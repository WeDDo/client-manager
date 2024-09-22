<?php

namespace App\Http\Controllers;

use App\DataTables\ChatRooms\ChatRoomDataTable;
use Illuminate\Http\JsonResponse;

class ChatRoomController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json((new ChatRoomDataTable())->get());
    }
}
