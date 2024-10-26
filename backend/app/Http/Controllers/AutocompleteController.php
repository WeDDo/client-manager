<?php

namespace App\Http\Controllers;

use App\Services\AutocompleteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function __construct(private AutocompleteService $autocompleteService)
    {
    }

    public function search(Request $request): JsonResponse
    {
        return response()->json($this->autocompleteService->search());
    }

    public function searchById(Request $request): JsonResponse
    {
        return response()->json($this->autocompleteService->searchById());
    }
}

