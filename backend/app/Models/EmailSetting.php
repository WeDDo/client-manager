<?php

namespace App\Models;

use App\DataTables\EmailSettings\EmailSettingDataTable;
use App\Traits\CreateUpdateUserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSetting extends Model
{
    use HasFactory, CreateUpdateUserTrait;

    public static string $smtpProtocol = 'smtp';
    public static string $imapProtocol = 'imap';

    protected $fillable = [
        'host',
        'port',
        'encryption',
        'validate_cert',
        'username',
        'password',
        'protocol',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getAdditionalData(): array
    {
        return [
            'data_table_item' => (new EmailSettingDataTable())->getItem($this->id),
        ];
    }
}
