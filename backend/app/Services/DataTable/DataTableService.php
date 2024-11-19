<?php

namespace App\Services\DataTable;

use App\Models\DataTable;
use App\Models\EmailInboxSetting;

class DataTableService
{
    public function clearFilter(array $data): void
    {
        $dataTableName = "App\DataTables\\{$data['name']}";

        $dataTable = DataTable::where('user_id', auth()->id())
            ->where('name', $dataTableName)
            ->first();

        $dataTable->update([
            'filters' => null,
        ]);
    }
}


