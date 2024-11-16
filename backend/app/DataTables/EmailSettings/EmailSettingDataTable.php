<?php

namespace App\DataTables\EmailSettings;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailSettingDataTable extends BaseDataTable
{
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
            ['name' => 'active', 'header' => 'Active', 'align' => 'left'],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = auth()->user()->emailSettings();

        $this->applyFilters($query);
        $this->applySorting($query);
        $emailSettings = $query->paginate($this->perPage);

        $columns = $this->getColumnItemClosures();

        $transformedItems = $emailSettings->getCollection()->map(function ($emailSetting) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($emailSetting);
            }
            return $rowData;
        });

        $emailSettings->setCollection(collect($transformedItems));

        return $emailSettings;
    }
}
