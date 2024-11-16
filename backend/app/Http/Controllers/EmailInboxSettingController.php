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
//        $this->emailInboxSettingService->getInboxesImap();
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
        $this->emailInboxSettingService->destroy($emailInboxSetting);
        return response()->json([], 204);
    }

    public function getInboxesImap(): JsonResponse
    {
        return response()->json($this->emailInboxSettingService->getInboxesImap());
    }

    public function createInboxes(): JsonResponse
    {
        $this->emailInboxSettingService->createInboxes(request()->all());
        return response()->json([], 201);
    }
}
