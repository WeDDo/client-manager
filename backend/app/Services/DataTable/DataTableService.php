<?php

namespace App\Services\DataTable;

use App\Models\DataTable;

class DataTableService
{
    public function updateActiveColumns(array $data): array
    {
        $dataTable = DataTable::query()->where([
            'name' => $data['name'],
            'user_id' => auth()->id(),
        ])->first();

        $dataTable->where([
            'name' => $data['name'],
            'user_id' => auth()->id(),
        ])?->update([
            'selected_columns' => json_encode($data['selected_columns'] ?? null)
        ]);

        return [];
    }

    public function clearFilter(array $data): void
    {
        $dataTable = DataTable::query()
            ->where('user_id', auth()->id())
            ->where('name', $data['name'])
            ->first();

        $dataTable?->update([
            'filters' => null,
        ]);
    }

    public function resetColumns(array $data): void
    {
        $dataTable = DataTable::query()
            ->where('user_id', auth()->id())
            ->where('name', $data['name'])
            ->first();

        $dataTable?->update([
            'selected_columns' => null,
        ]);
    }
}


