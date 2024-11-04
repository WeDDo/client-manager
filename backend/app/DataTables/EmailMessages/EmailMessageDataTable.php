<?php

namespace App\DataTables\EmailMessages;

use App\DataTables\BaseDataTable;
use App\Models\EmailSetting;
use App\Services\EmailMessageService;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailMessageDataTable extends BaseDataTable
{
    public function get(): array
    {
        $activeColumns = $this->getActiveColumns();
        $columns = array_keys($this->getColumnItemClosures());
        $items = $this->getItems();
        $additionalData = (new EmailMessageService())->getAdditionalData();

        return [
            'active_columns' => $activeColumns,
            'columns' => $columns,
            'items' => $items,
            'additional_data' => $additionalData,
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
            'unread_count' => function ($rowData) {
                return $rowData['unread_count'];
            },
        ];
    }

    public function getActiveColumns(): array
    {
        return [
            ['name' => 'subject', 'header' => 'Subject', 'align' => 'left', 'min_width' => 300],
            ['name' => 'from', 'header' => 'From', 'align' => 'left', 'min_width' => 125],
            ['name' => 'date', 'header' => 'Date', 'align' => 'left', 'min_width' => 150],
            ['name' => 'is_flagged', 'header' => 'Is flagged', 'align' => 'left', 'min_width' => 125],
            ['name' => 'is_answered', 'header' => 'Is answered', 'align' => 'left', 'min_width' => 125],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = auth()->user()->emailMessages()
            ->select('email_messages.*')
            ->leftJoin('email_messages as replies', 'email_messages.id', '=', 'replies.reply_to_email_message_id')
            ->where(function ($query) {
                // Select emails without further replies, including emails that aren't part of a thread
                $query->whereNull('replies.id')
                    ->orWhereNull('email_messages.reply_to_email_message_id');
            })
            ->when(request('selected_folder'), function ($query) {
                $query->where('email_messages.folder', request('selected_folder'));
            })
            ->orderByDesc('email_messages.date');

        $this->applyDefaultOrderBy($query);
        $items = $query->paginate($this->perPage);

        $items->getCollection()->transform(function ($email) {
            $unreadCount = 0;
            $currentEmail = $email;

            // Calculate unread count
            if (!$currentEmail->is_seen) {
                $unreadCount++;
            }

            while ($currentEmail->replyToEmailMessage) {
                if (!$currentEmail->replyToEmailMessage->is_seen) {
                    $unreadCount++;
                    break;
                }
                $currentEmail = $currentEmail->replyToEmailMessage;
            }

            $email->unread_count = $unreadCount;
            return $email;
        });

        // Get the column closures for transformation
        $columns = $this->getColumnItemClosures();

        // Transform each email message into the format defined by the column closures
        $transformedItems = $items->getCollection()->map(function ($emailMessage) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($emailMessage);
            }
            return $rowData;
        });

        // Replace the original collection with the transformed items
        $items->setCollection(collect($transformedItems));

        // Return the paginated object with transformed data
        return $items;
    }


    public function getItem(mixed $id): array
    {
        $emailMessage = auth()->user()->emailMessages()->where('id', $id)->first();

        $columns = $this->getColumnItemClosures();

        $rowData = [];
        foreach ($columns as $columnKey => $getColumnValue) {
            $rowData[$columnKey] = $getColumnValue($emailMessage);
        }

        return $rowData;
    }
}
