<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'unread_email_count' => auth()->user()->emailMessages()
                ->where('is_seen', false)
                ->count(),
        ]);
    }
}
