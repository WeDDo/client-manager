<?php

namespace App\Models;

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

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
