<?php

namespace App\DataTables\Partners\Contacts;

use App\DataTables\BaseDataTable;
use App\Models\Contact;
use App\Models\EmailSetting;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PartnerContactDataTable extends BaseDataTable
{
    public function getColumnItemClosures(): array
    {
        return [
            'id' => fn($rowData) => $rowData['id'],
            'name' => fn($rowData) => $rowData['name'],
            'company_name' => fn($rowData) => $rowData['company_name'],
            'position' => fn($rowData) => $rowData['position'],
            'phone1' => fn($rowData) => $rowData['phone1'],
            'phone2' => fn($rowData) => $rowData['phone2'],
            'email1' => fn($rowData) => $rowData['email1'],
            'email2' => fn($rowData) => $rowData['email2'],
            'birthday' => fn($rowData) => $rowData['birthday'],
            'notes' => fn($rowData) => $rowData['notes'],
            'address1' => fn($rowData) => $rowData['address1'],
            'address2' => fn($rowData) => $rowData['address2'],
            'city' => fn($rowData) => $rowData['city'],
            'state' => fn($rowData) => $rowData['state'],
            'postal_code' => fn($rowData) => $rowData['postal_code'],
            'country' => fn($rowData) => $rowData['country'],
            'website' => fn($rowData) => $rowData['website'],
            'preferred_contact_method' => fn($rowData) => $rowData['preferred_contact_method'],
            'status' => fn($rowData) => $rowData['status'],
            'last_contacted_at' => fn($rowData) => $rowData['last_contacted_at'],
            'partner_id' => fn($rowData) => $rowData['partner_id'],
            'created_by' => fn($rowData) => $rowData['created_by'],
            'updated_by' => fn($rowData) => $rowData['updated_by'],
            'created_at' => fn($rowData) => Carbon::parse($rowData['created_at'])->format('Y-m-d H:i:s'),
            'updated_at' => fn($rowData) => Carbon::parse($rowData['updated_at'])->format('Y-m-d H:i:s'),
        ];
    }

    public function getColumns(): array
    {
        return [
            ['name' => 'name', 'header' => 'Name', 'align' => 'left', 'min_width' => 150],
            ['name' => 'company_name', 'header' => 'Company', 'align' => 'left', 'min_width' => 150],
            ['name' => 'position', 'header' => 'Position', 'align' => 'left', 'min_width' => 150],
            ['name' => 'phone1', 'header' => 'Primary Phone', 'align' => 'left', 'min_width' => 150],
            ['name' => 'phone2', 'header' => 'Secondary Phone', 'align' => 'left', 'min_width' => 150],
            ['name' => 'email1', 'header' => 'Primary Email', 'align' => 'left', 'min_width' => 150],
            ['name' => 'email2', 'header' => 'Secondary Email', 'align' => 'left', 'min_width' => 150],
            ['name' => 'birthday', 'header' => 'Birthday', 'align' => 'center', 'min_width' => 150],
            ['name' => 'address1', 'header' => 'Address 1', 'align' => 'left', 'min_width' => 150],
            ['name' => 'address2', 'header' => 'Address 2', 'align' => 'left', 'min_width' => 150],
            ['name' => 'city', 'header' => 'City', 'align' => 'left', 'min_width' => 150],
            ['name' => 'state', 'header' => 'State', 'align' => 'left', 'min_width' => 150],
            ['name' => 'postal_code', 'header' => 'Postal Code', 'align' => 'center', 'min_width' => 150],
            ['name' => 'country', 'header' => 'Country', 'align' => 'left', 'min_width' => 150],
            ['name' => 'website', 'header' => 'Website', 'align' => 'left', 'min_width' => 150],
            ['name' => 'preferred_contact_method', 'header' => 'Preferred Contact', 'align' => 'left', 'min_width' => 150],
            ['name' => 'status', 'header' => 'Status', 'align' => 'center', 'min_width' => 150],
            ['name' => 'last_contacted_at', 'header' => 'Last Contacted', 'align' => 'right', 'min_width' => 200],
            ['name' => 'partner_id', 'header' => 'Partner ID', 'align' => 'center', 'min_width' => 150],
            ['name' => 'created_at', 'header' => 'Created At', 'align' => 'right', 'min_width' => 200],
            ['name' => 'updated_at', 'header' => 'Updated At', 'align' => 'right', 'min_width' => 200],
        ];
    }

    public function getItems(): LengthAwarePaginator
    {
        $query = Contact::query()
            ->where('partner_id', $this->additionalData['partner_id']);

        $this->applyFilters($query);
        $this->applySorting($query);
        $items = $query->paginate($this->perPage);

        $columns = $this->getColumnItemClosures();
        $transformedItems = $items->getCollection()->map(function ($item) use ($columns) {
            $rowData = [];
            foreach ($columns as $columnKey => $getColumnValue) {
                $rowData[$columnKey] = $getColumnValue($item);
            }
            return $rowData;
        });

        $items->setCollection(collect($transformedItems));

        return $items;
    }
}
