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

    public function getItems(): array
    {
        $emailSettings = auth()->user()->emailInboxSettings;
        $columns = $this->getColumnItemClosures();

        $data = [];
        foreach ($emailSettings as $emailSetting) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($emailSetting);
            }
            $data[] = $rowData;
        }

        return $data;
    }
}
