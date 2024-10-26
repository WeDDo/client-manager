<?php

namespace App\Http\Controllers;

use App\DataTables\Contacts\ContactDataTable;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private ContactService $Contactservice)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new ContactDataTable())->get());
    }

    public function show(Contact $contact): JsonResponse
    {
        return response()->json([
            'item' => $this->Contactservice->show($contact),
        ]);
    }

    public function store(ContactRequest $request): JsonResponse
    {
        $contact = $this->Contactservice->store($request->validated());

        return response()->json([
            'item' => $contact,
        ]);
    }

    public function update(ContactRequest $request, Contact $contact): JsonResponse
    {
        $this->Contactservice->update($request->validated(), $contact);

        return response()->json([
            'item' => $contact,
        ]);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        return response()->json([], 204);
    }
}
