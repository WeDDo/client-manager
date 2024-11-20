<?php

namespace App\DataTables\Partners;

use App\DataTables\BaseDataTable;
use App\Models\Partner;
use Illuminate\Pagination\LengthAwarePaginator;

class PartnerDataTable extends BaseDataTable
{
    public function getColumnItemClosures(): array
    {
        return [
            'id' => function ($rowData) {
                return $rowData['id'];
            },
            'id_name' => function ($rowData) {
                return $rowData['id_name'];
            },
            'name' => function ($rowData) {
                return $rowData['name'];
            },
            'name2' => function ($rowData) {
                return $rowData['name2'];
            },
            'legal_status' => function ($rowData) {
                return $rowData['legal_status'];
            },
            'email' => function ($rowData) {
                return $rowData['email'];
            },
            'phone' => function ($rowData) {
                return $rowData['phone'];
            },
        ];
    }

    public function getColumns(): array
    {
        return [
            ['name' => 'id_name', 'header' => 'ID name', 'align' => 'left'],
            ['name' => 'name', 'header' => 'Name', 'align' => 'left'],
            ['name' => 'name2', 'header' => 'Name 2', 'align' => 'left'],
            ['name' => 'legal_status', 'header' => 'Legal status', 'align' => 'left'],
            ['name' => 'email', 'header' => 'Email', 'align' => 'left'],
            ['name' => 'phone', 'header' => 'Phone', 'align' => 'left'],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = Partner::query();

        $this->applyFilters($query);
        $this->applySorting($query);
        $items = $query->paginate($this->perPage);

        $columns = $this->getColumnItemClosures();
        $transformedItems = $items->getCollection()->map(function ($item) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($item);
            }
            return $rowData;
        });
        $items->setCollection(collect($transformedItems));

        return $items;
    }
}
