<?php

namespace App\Http\Controllers;

use App\DataTables\Partners\PartnerDataTable;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    public function __construct(private PartnerService $partnerService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new PartnerDataTable())->get());
    }

    public function show(Partner $partner): JsonResponse
    {
        $partner = $this->partnerService->show($partner);

        return response()->json([
            'item' => $partner,
            'additional' => $partner->getAdditionalData(),
        ]);
    }

    public function store(PartnerRequest $request): JsonResponse
    {
        $partner = $this->partnerService->store($request->validated());

        return response()->json([
            'item' => $partner,
            'additional' => $partner->getAdditionalData(),
        ]);
    }

    public function update(PartnerRequest $request, Partner $partner): JsonResponse
    {
        $this->partnerService->update($request->validated(), $partner);

        return response()->json([
            'item' => $partner,
            'additional' => $partner->getAdditionalData(),
        ]);
    }

    public function destroy(Partner $partner): JsonResponse
    {
        $partner->delete();
        return response()->json([], 204);
    }
}
