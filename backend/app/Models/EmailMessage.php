<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'subject',
        'from',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'date',
        'body_text',
        'body_html',
        'is_seen',
        'is_flagged',
        'is_answered',
        'folder',
        'thread_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
