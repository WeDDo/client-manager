<?php

namespace App\Http\Controllers;

use App\DataTables\Contacts\ContactDataTable;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new ContactDataTable())->get());
    }

    public function show(Contact $contact): JsonResponse
    {
        return response()->json([
            'item' => $this->contactService->show($contact),
            'additional' => $contact->getAdditionalData(),
        ]);
    }

    public function store(ContactRequest $request): JsonResponse
    {
        $contact = $this->contactService->store($request->validated());

        return response()->json([
            'item' => $contact,
            'additional' => $contact->getAdditionalData(),
        ]);
    }

    public function update(ContactRequest $request, Contact $contact): JsonResponse
    {
        $this->contactService->update($request->validated(), $contact);

        return response()->json([
            'item' => $contact,
            'additional' => $contact->getAdditionalData(),
        ]);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        return response()->json([], 204);
    }
}
