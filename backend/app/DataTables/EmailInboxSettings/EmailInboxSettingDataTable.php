<?php

namespace App\DataTables\EmailInboxSettings;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailInboxSettingDataTable extends BaseDataTable
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
            'name' => function ($rowData) {
                return $rowData['name'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'name', 'header' => 'Name', 'align' => 'left'],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = auth()->user()->emailInboxSettings();

        $this->applyFilters($query);
        $this->applyDefaultOrderBy($query);
        $items = $query->paginate($this->perPage);

        $columns = $this->getColumnItemClosures();

        $transformedItems = $items->getCollection()->map(function ($emailSetting) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($emailSetting);
            }
            return $rowData;
        });

        $items->setCollection(collect($transformedItems));

        return $items;
    }
}
