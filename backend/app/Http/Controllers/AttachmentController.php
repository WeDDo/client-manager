<?php

namespace App\Http\Controllers;

use App\DataTables\Attachments\AttachmentDataTable;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function __construct(private AttachmentService $attachmentService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new AttachmentDataTable())->get());
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        return response()->json([], 204);
    }

    public function downloadAttachment(Attachment $attachment): StreamedResponse
    {
        return $this->attachmentService->downloadAttachment($attachment);
    }
}
