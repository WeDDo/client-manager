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
            'filters' => $this->getDefaultFilters(),
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

    public function getColumns(): array
    {
        return [
            ['name' => 'name', 'header' => 'Name', 'align' => 'left', 'min_width' => 300],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = ChatRoom::query();

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

//    public function getItem(mixed $id): array
//    {
//        $emailSetting = auth()->user()->emailMessages()->where('id', $id)->first();
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
