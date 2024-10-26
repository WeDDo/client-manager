<?php

namespace App\Models;

use App\DataTables\Partners\Contacts\PartnerContactDataTable;
use App\Services\AutocompleteService;
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
            ]))->get(),
//            'autocomplete_data' => $this->getAutocompleteData(),
        ];
    }

//    public function getAutocompleteData(): array
//    {
//        $config = [
//            'partner_id' => [
//                'table' => 'partners',
//                'search_fields' => ['id_name', 'name'],
//            ]
//        ];
//
//        $autocompleteData = [];
//        foreach ($config as $key => $settings) {
//            $autocompleteData[$key] = [
//                'table' => $settings['table'],
//                'search_fields' => $settings['search_fields'],
//                'item' => (new AutocompleteService())->searchById(
//                    $settings['table'],
//                    $this->id,
//                    $settings['search_fields']
//                ),
//            ];
//        }
//
//        return $autocompleteData;
//    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
