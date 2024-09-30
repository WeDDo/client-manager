<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
