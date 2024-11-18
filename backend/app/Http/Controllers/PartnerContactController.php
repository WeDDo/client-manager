<?php

namespace App\Http\Controllers;

use App\DataTables\Partners\Contacts\PartnerContactDataTable;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;

class PartnerContactController extends Controller
{
    public function index(Partner $partner): JsonResponse
    {
        return response()->json((new PartnerContactDataTable([
            'partner_id' => $partner->id,
        ]))->get());
    }
}
