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
//        $imapConfig = $this->emailSettingService->setImapEmailConfig();
//
//        $client = Client::make($imapConfig);
//        $client->connect();
//
//        $folder = $client->getFolder('INBOX');
//        $messages = $folder->query()->since(now()->subDays(7))->get();
//
//        foreach ($messages as $message) {
////            dd(
////                $message->getSubject(),            // Subject
////                $message->getHTMLBody(),           // HTML Body
////                $message->getTextBody(),           // Plain Text Body
////                $message->getFrom(),               // Sender
////                $message->getTo(),                 // Recipients
////                $message->getDate()->format('Y-m-d H:i:s'), // Date
////                $message->getAttachments()         // Attachments
////            );
//        }

        return response()->json((new EmailSettingDataTable())->get());
    }

    public function show(EmailSetting $emailSetting): JsonResponse
    {
        $emailSetting = $this->emailSettingService->show($emailSetting);

        return response()->json([
            'item' => $emailSetting,
        ]);
    }

    public function store(EmailSettingRequest $request): JsonResponse
    {
        $emailSetting = $this->emailSettingService->store($request->validated());

        return response()->json([
            'item' => $emailSetting,
        ]);
    }

    public function update(EmailSettingRequest $request, EmailSetting $emailSetting): JsonResponse
    {
        $this->emailSettingService->update($request->validated(), $emailSetting);

        return response()->json([
            'item' => $emailSetting,
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
        ]);
    }
}
