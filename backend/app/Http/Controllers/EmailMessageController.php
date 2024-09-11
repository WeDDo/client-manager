<?php

namespace App\Http\Controllers;

use App\DataTables\EmailMessages\EmailMessageDataTable;
use App\Http\Requests\EmailMessageRequest;
use App\Models\EmailMessage;
use App\Services\EmailMessageService;
use Illuminate\Http\JsonResponse;

class EmailMessageController extends Controller
{
    public function __construct(private EmailMessageService $emailMessageService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new EmailMessageDataTable())->get());
    }

    public function show(EmailMessage $emailMessage): JsonResponse
    {
        $emailMessage = $this->emailMessageService->show($emailMessage);

        return response()->json([
            'item' => $emailMessage,
        ]);
    }

    public function update(EmailMessageRequest $request, EmailMessage $emailMessage): JsonResponse
    {
        $emailMessage->update($request->validated());

        return response()->json([
            'item' => $emailMessage
        ]);
    }

    public function getEmailsUsingImap(): JsonResponse
    {
        $createdEmailMessages = $this->emailMessageService->getEmailsUsingImap();

        return response()->json([
            'items' => $createdEmailMessages
        ]);
    }
}
