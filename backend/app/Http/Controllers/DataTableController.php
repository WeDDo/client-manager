<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\DataTable\DataTableService;

class DataTableController extends Controller
{
    public function __construct(private DataTableService $dataTableService)
    {
    }

    // todo needs to be refactored to here for simpler handling
    public function applySorting(): JsonResponse
    {
        return response()->json([]);
    }

    public function updateActiveColumns(Request $request): JsonResponse
    {
        $this->dataTableService->updateActiveColumns($request->all());
        return response()->json([]);
    }

    public function resetColumns(Request $request): JsonResponse
    {
        $this->dataTableService->resetColumns($request->all());
        return response()->json([]);
    }


    public function clearFilter(Request $request): JsonResponse
    {
        $this->dataTableService->clearFilter($request->all());
        return response()->json([]);
    }
}
