<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Success!',
            'data' => $request->validated(),
        ]);
    }
}
