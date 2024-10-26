<?php

namespace App\Models;

use App\Traits\CreateUpdateUserTrait;
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

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
