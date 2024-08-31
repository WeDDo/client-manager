<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'item' => $this->settingService->index(),
        ]);
    }

    public function getSettings(): JsonResponse
    {
        return response()->json(['items' => Setting::all()]);
    }

    public function updateAll(SettingRequest $request): JsonResponse
    {
        $this->settingService->updateAll($request->validated());

        return response()->json([
            'item' => $this->settingService->index(),
        ]);
    }
}
