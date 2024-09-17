<?php

namespace App\DataTables\EmailSettings;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;

class EmailSettingDataTable extends BaseDataTable
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
            'host' => function ($rowData) {
                return $rowData['host'];
            },
            'port' => function ($rowData) {
                return $rowData['port'];
            },
            'encryption' => function ($rowData) {
                return $rowData['encryption'];
            },
            'validate_cert' => function ($rowData) {
                return $rowData['validate_cert'];
            },
            'username' => function ($rowData) {
                return $rowData['username'];
            },
            'password' => function ($rowData) {
                return $rowData['password'];
            },
            'protocol' => function ($rowData) {
                return $rowData['protocol'];
            },
            'active' => function ($rowData) {
                return $rowData['active'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'host', 'header' => 'Host', 'align' => 'left'],
            ['name' => 'port', 'header' => 'Port', 'align' => 'right'],
            ['name' => 'encryption', 'header' => 'Encryption', 'align' => 'left'],
            ['name' => 'username', 'header' => 'Username', 'align' => 'left'],
            ['name' => 'protocol', 'header' => 'Protocol', 'align' => 'left'],
        ];
    }

    public function getItems(): array
    {
        $emailSettings = auth()->user()->emailSettings;
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

    public function getItem(mixed $id): array
    {
        $emailSetting = auth()->user()->emailSettings()->where('id', $id)->first();

        $columns = $this->getColumnItemClosures();

        $rowData = [];
        foreach ($columns as $columnKey => $getColumnValue) {
            $rowData[$columnKey] = $getColumnValue($emailSetting);
        }

        return $rowData;
    }
}
