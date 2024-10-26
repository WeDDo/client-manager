<?php

namespace App\Models;

use App\DataTables\Partners\Contacts\PartnerContactDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_name',
        'name',
        'name2',
        'legal_status',
        'email',
        'phone',
    ];

    public static function getLegalStatuses(): array
    {
        return [
            'F',
            'J',
        ];
    }

    public function getAllRelations(): array
    {
        return [];
    }

    public function getAdditionalData(): array
    {
        return [
            'contacts_data_table' => (new PartnerContactDataTable([
                'partner_id' => $this->id,
            ]))->get()
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
