<?php

namespace App\Http\Controllers;

use App\DataTables\EmailInboxSettings\EmailInboxSettingDataTable;
use App\Http\Requests\EmailInboxSettingRequest;
use App\Models\EmailInboxSetting;
use App\Services\EmailInboxSettingService;
use Illuminate\Http\JsonResponse;

class EmailInboxSettingController extends Controller
{
    public function __construct(private EmailInboxSettingService $emailInboxSettingService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new EmailInboxSettingDataTable())->get());
    }

    public function show(EmailInboxSetting $emailInboxSetting): JsonResponse
    {
        return response()->json([
            'item' => $emailInboxSetting,
        ]);
    }

    public function store(EmailInboxSettingRequest $request): JsonResponse
    {
        $emailInboxSetting = $this->emailInboxSettingService->store($request->validated());

        return response()->json([
            'item' => $emailInboxSetting,
        ]);
    }

    public function update(EmailInboxSettingRequest $request, EmailInboxSetting $emailInboxSetting): JsonResponse
    {
        $this->emailInboxSettingService->update($request->validated(), $emailInboxSetting);

        return response()->json([
            'item' => $emailInboxSetting,
        ]);
    }

    public function destroy(EmailInboxSetting $emailInboxSetting): JsonResponse
    {
        $emailInboxSetting->delete();
        return response()->json([], 204);    }
}
