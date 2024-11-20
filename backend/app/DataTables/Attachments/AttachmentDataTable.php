<?php

namespace App\DataTables\Attachments;

use App\DataTables\BaseDataTable;
use App\Models\Attachment;
use App\Models\EmailSetting;
use Illuminate\Pagination\LengthAwarePaginator;

class AttachmentDataTable extends BaseDataTable
{
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

    public function getColumns(): array
    {
        return [
            ['name' => 'id', 'header' => 'Id', 'align' => 'left'],
            ['name' => 'filename', 'header' => 'Filename', 'align' => 'left'],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $attachments = Attachment::query()
            ->where('related_name', request('related_name'))
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
}
