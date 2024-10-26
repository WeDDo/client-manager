<?php

namespace App\Http\Controllers;

use App\DataTables\Partners\Contacts\PartnerContactDataTable;
use App\DataTables\Partners\PartnerDataTable;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Http\JsonResponse;

class PartnerContactController extends Controller
{
    public function __construct(private PartnerService $partnerService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json((new PartnerContactDataTable())->get());
    }
}
