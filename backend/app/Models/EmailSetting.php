<?php

namespace App\Models;

use App\DataTables\EmailSettings\EmailSettingDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSetting extends Model
{
    use HasFactory;

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
        'user_id',
    ];

    public function getAdditionalData(): array
    {
        return [
            'data_table_item' => (new EmailSettingDataTable())->getItem($this->id),
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
