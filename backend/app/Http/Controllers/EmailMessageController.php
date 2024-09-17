<?php

namespace App\Http\Controllers;

use App\DataTables\EmailMessages\EmailMessageDataTable;
use App\Http\Requests\EmailMessageRequest;
use App\Http\Requests\SendEmailRequest;
use App\Models\EmailInboxSetting;
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
            'relations' => $emailMessage->getAllRelations(),
            'additional'=> $emailMessage->getAdditionalData(),
        ]);
    }

    public function update(EmailMessageRequest $request, EmailMessage $emailMessage): JsonResponse
    {
        $emailMessage->update($request->validated());

        return response()->json([
            'item' => $emailMessage
        ]);
    }

    public function getAdditionalData(): JsonResponse
    {
        return response()->json($this->emailMessageService->getAdditionalData());
    }

    public function getEmailsUsingImap(): JsonResponse
    {
        $createdEmailMessages = $this->emailMessageService->getEmailsUsingImap();

        return response()->json([
            'items' => $createdEmailMessages
        ]);
    }

    public function sendEmailUsingSmtp(SendEmailRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->emailMessageService->sendEmailUsingSMTP(
            $data['to_emails'],
            $data['cc_emails'],
            $data['bcc_emails'],
        );

        return response()->json([
            'message' => 'Email sent successfully!',
        ]);
    }
}
