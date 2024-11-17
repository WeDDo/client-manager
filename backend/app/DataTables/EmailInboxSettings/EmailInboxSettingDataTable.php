<?php

namespace App\DataTables\EmailInboxSettings;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailInboxSettingDataTable extends BaseDataTable
{
    protected function setFilterFieldTypes(): array
    {
        return [
            'auto_set_is_seen' => self::$boolFieldType,
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
            'auto_set_is_seen' => function ($rowData) {
                return $rowData['auto_set_is_seen'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'name', 'header' => 'Name', 'align' => 'left'],
            ['name' => 'auto_set_is_seen', 'header' => 'Auto set is seen', 'align' => 'left'],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = auth()->user()->emailInboxSettings();

        $this->applyFilters($query);
        $this->applySorting($query);
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
