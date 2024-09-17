<?php

namespace App\DataTables\EmailMessages;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;
use App\Services\EmailMessageService;

class EmailMessageDataTable extends BaseDataTable
{
    public function get(): array
    {
        return [
            'active_columns' => $this->getActiveColumns(),
            'columns' => array_keys($this->getColumnItemClosures()),
            'items' => $this->getItems(),
            'additional_data' => (new EmailMessageService())->getAdditionalData(),
        ];
    }

    public function getColumnItemClosures(): array
    {
        return [
            'id' => function ($rowData) {
                return $rowData['id'];
            },
            'subject' => function ($rowData) {
                return $rowData['subject'];
            },
            'date' => function ($rowData) {
                return $rowData['date'];
            },
            'from' => function ($rowData) {
                return $rowData['from'];
            },
            'is_seen' => function ($rowData) {
                return $rowData['is_seen'];
            },
            'is_flagged' => function ($rowData) {
                return $rowData['is_flagged'];
            },
            'is_answered' => function ($rowData) {
                return $rowData['is_answered'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'subject', 'header' => 'Subject', 'align' => 'left', 'min_width' => 300],
            ['name' => 'date', 'header' => 'Date', 'align' => 'left', 'min_width' => 150],
            ['name' => 'from', 'header' => 'From', 'align' => 'left', 'min_width' => 125],
            ['name' => 'is_seen', 'header' => 'Is seen', 'align' => 'left', 'min_width' => 125],
            ['name' => 'is_flagged', 'header' => 'Is flagged', 'align' => 'left', 'min_width' => 125],
            ['name' => 'is_answered', 'header' => 'Is answered', 'align' => 'left', 'min_width' => 125],
        ];
    }

    public function getItems(): array
    {
        $emailSettings = auth()->user()->emailMessages()
            ->when(request('selected_folder'), function ($query) {
                $query->where('folder', request('selected_folder'));
            }) // todo fix folder
            ->orderByDesc('date')
            ->get();

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
        $emailSetting = auth()->user()->emailMessages()->where('id', $id)->first();

        $columns = $this->getColumnItemClosures();

        $rowData = [];
        foreach ($columns as $columnKey => $getColumnValue) {
            $rowData[$columnKey] = $getColumnValue($emailSetting);
        }

        return $rowData;
    }
}
