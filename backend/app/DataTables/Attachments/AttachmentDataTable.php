<?php

namespace App\DataTables\Attachments;

use App\DataTables\BaseDataTable;
use App\Models\Attachment;
use App\Models\EmailSetting;
use Illuminate\Pagination\LengthAwarePaginator;

class AttachmentDataTable extends BaseDataTable
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
            'filename' => function ($rowData) {
                return $rowData['filename'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'id', 'header' => 'Id', 'align' => 'left'],
            ['name' => 'filename', 'header' => 'Filename', 'align' => 'left'],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $attachments = Attachment::where('related_name', request('related_name'))
            ->where('related_id', request('related_id'))
            ->get();
        $columns = $this->getColumnItemClosures();

        $data = [];
        foreach ($attachments as $attachment) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($attachment);
            }
            $data[] = $rowData;
        }

        return $data;
    }

//    public function getItem(mixed $id): array
//    {
//        $emailSetting = auth()->user()->emailSettings()->where('id', $id)->first();
//
//        $columns = $this->getColumnItemClosures();
//
//        $rowData = [];
//        foreach ($columns as $columnKey => $getColumnValue) {
//            $rowData[$columnKey] = $getColumnValue($emailSetting);
//        }
//
//        return $rowData;
//    }
}
