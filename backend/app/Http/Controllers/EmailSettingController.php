<?php

namespace App\Http\Controllers;

use App\DataTables\EmailSettings\EmailSettingDataTable;
use App\Http\Requests\EmailSettingRequest;
use App\Models\EmailSetting;
use App\Services\EmailSettingService;
use Illuminate\Http\JsonResponse;

class EmailSettingController extends Controller
{
    public function __construct(private EmailSettingService $emailSettingService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new EmailSettingDataTable())->get());
    }

    public function show(EmailSetting $emailSetting): JsonResponse
    {
        $emailSetting = $this->emailSettingService->show($emailSetting);

        return response()->json([
            'item' => $emailSetting,
            'additional' => $emailSetting->getAdditionalData(),
        ]);
    }

    public function store(EmailSettingRequest $request): JsonResponse
    {
        $emailSetting = $this->emailSettingService->store($request->validated());

        return response()->json([
            'item' => $emailSetting,
            'additional' => $emailSetting->getAdditionalData(),
        ]);
    }

    public function update(EmailSettingRequest $request, EmailSetting $emailSetting): JsonResponse
    {
        $this->emailSettingService->update($request->validated(), $emailSetting);

        return response()->json([
            'item' => $emailSetting,
            'additional' => $emailSetting->getAdditionalData(),
        ]);
    }

    public function destroy(EmailSetting $emailSetting): JsonResponse
    {
        $emailSetting->delete();
        return response()->json([], 204);
    }

    public function copy(EmailSetting $emailSetting): JsonResponse
    {
        $emailSettingCopy = $this->emailSettingService->copy($emailSetting);

        return response()->json([
            'item' => $emailSettingCopy,
            'additional' => $emailSettingCopy->getAdditionalData(),
        ]);
    }

    public function checkConnection(EmailSetting $emailSetting): JsonResponse
    {
        $this->emailSettingService->checkConnection($emailSetting);

        return response()->json([
            'message' => 'success!',
        ]);
    }
}
