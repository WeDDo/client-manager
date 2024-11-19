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

    public function clearFilter(Request $request): JsonResponse
    {
        $this->dataTableService->clearFilter($request->all());
        return response()->json([]);
    }
}
