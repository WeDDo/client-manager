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
        'reply_to_email_message_id',
        'user_id',
    ];

    public function getAdditionalData(): array
    {
        return [
//            'conversation' => $this->getEmailConversation(),
        ];
    }

    // todo
//    public function getAllRepliedToEmailMessages()
//    {
//
//    }

//    public function getEmailConversation(): array
//    {
//        $conversation = [];
//        $this->collectEmailThread($this, $conversation);
//
//        return $conversation;
//    }
//
//    private function collectEmailThread(EmailMessage $emailMessage, array &$conversation): void
//    {
//        $conversation[] = $emailMessage;
//
//        if ($emailMessage->replyToMessage) {
//            $this->collectEmailThread($emailMessage->replyToMessage, $conversation);
//        }
//    }

    public function replyToEmailMessage(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'reply_to_email_message_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
