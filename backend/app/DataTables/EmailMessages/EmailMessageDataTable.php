<?php

namespace App\DataTables\EmailMessages;

use App\DataTables\BaseDataTable;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailMessageDataTable extends BaseDataTable
{
    protected function setFilterFieldTypes(): array
    {
        return [
            'date' => self::$dateFieldType,
            'from' => self::$dateFieldType,
            'is_seen' => self::$boolFieldType,
            'is_flagged' => self::$boolFieldType,
            'is_answered' => self::$boolFieldType,
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

    public function getColumns(): array
    {
        return [
            ['name' => 'subject', 'header' => 'Subject', 'align' => 'left', 'min_width' => 300],
            ['name' => 'from', 'header' => 'From', 'align' => 'left', 'min_width' => 125],
            ['name' => 'date', 'header' => 'Date', 'align' => 'left', 'min_width' => 150],
            ['name' => 'is_seen', 'header' => 'Is seen', 'align' => 'left', 'min_width' => 125],
            ['name' => 'is_flagged', 'header' => 'Is flagged', 'align' => 'left', 'min_width' => 125],
            ['name' => 'is_answered', 'header' => 'Is answered', 'align' => 'left', 'min_width' => 125],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = auth()->user()->emailMessages();

        $this->applyFilters($query);
        $query->when(request('selected_folder'), function ($query) {
            $query->where('folder', request('selected_folder'));
        });

        $this->applySorting($query);

        $items = $query->paginate($this->perPage);

        $columns = $this->getColumnItemClosures();
        $transformedItems = $items->getCollection()->map(function ($emailMessage) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($emailMessage);
            }
            return $rowData;
        });

        $items->setCollection(collect($transformedItems));

        return $items;
    }
}
