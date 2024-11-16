<?php

namespace App\DataTables\Partners;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;
use App\Models\Partner;
use Illuminate\Pagination\LengthAwarePaginator;

class PartnerDataTable extends BaseDataTable
{
    public function get(): array
    {
        return [
            'active_columns' => $this->getActiveColumns(),
            'columns' => array_keys($this->getColumnItemClosures()),
            'items' => $this->getItems(),
            'filters' => $this->getDefaultFilters(),
        ];
    }

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

    public function getActiveColumns(): array
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
//
//    public function getDefaultFilters(): array
//    {
//        return array_merge(parent::getDefaultFilters(), [
//            [
//                'name' => 'name',
//                'label' => 'Name',
//                'operator' => '=',
//                'value' => null,
//            ],
//        ]);
//    }

    public function getItems(): LengthAwarePaginator
    {
        $query = Partner::query();

        $this->applyFilters($query);
        $this->applySorting($query);
        $items = $query->paginate($this->perPage);

        // Get the column closures
        $columns = $this->getColumnItemClosures();

        // Map through each paginated item and apply the closures
        $transformedItems = $items->getCollection()->map(function ($item) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($item);
            }
            return $rowData;
        });

        // Replace the original items collection with the transformed data
        $items->setCollection(collect($transformedItems));

        // Return the paginated object with transformed items
        return $items;
    }

    public function getItem(mixed $id): array
    {
        $item = Partner::where('id', $id)->first();

        $columns = $this->getColumnItemClosures();

        $rowData = [];
        foreach ($columns as $columnKey => $getColumnValue) {
            $rowData[$columnKey] = $getColumnValue($item);
        }

        return $rowData;
    }
}
