<?php

namespace App\DataTables\ChatRooms;

use App\DataTables\BaseDataTable;
use App\Models\ChatRoom;
use App\Models\EmailSetting;
use App\Services\EmailMessageService;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatRoomDataTable extends BaseDataTable
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
            'name' => function ($rowData) {
                return $rowData['name'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'name', 'header' => 'Name', 'align' => 'left', 'min_width' => 300],
//            ['name' => 'date', 'header' => 'Date', 'align' => 'left', 'min_width' => 150],
//            ['name' => 'from', 'header' => 'From', 'align' => 'left', 'min_width' => 125],
//            ['name' => 'is_seen', 'header' => 'Is seen', 'align' => 'left', 'min_width' => 125],
//            ['name' => 'is_flagged', 'header' => 'Is flagged', 'align' => 'left', 'min_width' => 125],
//            ['name' => 'is_answered', 'header' => 'Is answered', 'align' => 'left', 'min_width' => 125],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
//        $chatRooms = auth()->user()->chatRooms()
//            ->get();
        $chatRooms = ChatRoom::all();

        $columns = $this->getColumnItemClosures();

        $data = [];
        foreach ($chatRooms as $chatRoom) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($chatRoom);
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
