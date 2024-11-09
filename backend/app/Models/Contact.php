<?php

namespace App\Models;

use App\Services\AutocompleteService;
use App\Traits\CreateUpdateUserTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory, CreateUpdateUserTrait;

    protected $fillable = [
        'name',
        'company_name',
        'position',
        'phone1',
        'phone2',
        'email1',
        'email2',
        'birthday',
        'notes',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'preferred_contact_method',
        'status',
        'last_contacted_at',
        'partner_id',
    ];

    public function getAdditionalData(): array
    {
        return [
            'autocomplete_data' => $this->getAutocompleteData(),
        ];
    }

    public function getAutocompleteData(): array
    {
        $config = [
            'partner_id' => [
                'table' => 'partners',
                'search_fields' => ['id_name', 'name'],
            ]
        ];

        $autocompleteData = [];
        foreach ($config as $key => $settings) {
            $autocompleteData[$key] = [
                'table' => $settings['table'],
                'search_fields' => $settings['search_fields'],
                'item' => (new AutocompleteService())->searchById(
                    $settings['table'],
                    $this->partner_id,
                    $settings['search_fields']
                ),
            ];
        }

        return $autocompleteData;
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
