<?php

namespace App\DataTables\EmailInboxSettings;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;

class EmailInboxSettingDataTable extends BaseDataTable
{
    public function get(): array
    {
        return [
            'active_columns' => $this->getActiveColumns(),
            'columns' => array_keys($this->getColumnItemClosures()),
            'items' => $this->getItems(),
            'items_total_count' => auth()->user()->emailInboxSettings()->count(),
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

    public function getItems()
    {
        $items = auth()->user()->emailInboxSettings()->paginate($this->perPage);

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
