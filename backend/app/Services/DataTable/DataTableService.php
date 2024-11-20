<?php

namespace App\Services\DataTable;

use App\Models\DataTable;

class DataTableService
{
    public function clearFilter(array $data): void
    {
        $dataTable = DataTable::where('user_id', auth()->id())
            ->where('name', $data['name'])
            ->first();

        $dataTable?->update([
            'filters' => null,
        ]);
    }
}


