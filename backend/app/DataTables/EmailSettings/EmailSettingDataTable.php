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
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'id', 'header' => 'Id', 'align' => 'left'],
            ['name' => 'host', 'header' => 'Host', 'align' => 'left'],
            ['name' => 'port', 'header' => 'Port', 'align' => 'right'],
            ['name' => 'encryption', 'header' => 'Encryption', 'align' => 'left'],
//            ['name' => 'validate_cert', 'header' => 'Validate Cert', 'align' => 'left'],
            ['name' => 'username', 'header' => 'Username', 'align' => 'left'],
            ['name' => 'protocol', 'header' => 'Protocol', 'align' => 'left'],
        ];
    }

    public function getItems(): array
    {
        $emailSettings = EmailSetting::all();

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
