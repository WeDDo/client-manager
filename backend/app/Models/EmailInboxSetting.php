<?php

namespace App\Models;

use App\Traits\CreateUpdateUserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailInboxSetting extends Model
{
    use HasFactory, CreateUpdateUserTrait;

    protected $fillable = [
        'name',
        'auto_set_is_seen',
    ];
}
