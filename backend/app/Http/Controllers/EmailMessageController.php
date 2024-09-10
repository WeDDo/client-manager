<?php

namespace App\Http\Controllers;

use App\DataTables\EmailMessages\EmailMessageDataTable;
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
        return response()->json([
            'item' => $emailMessage,
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
