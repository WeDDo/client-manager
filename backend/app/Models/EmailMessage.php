<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getAllRelations(): array
    {
        return [
            'attachments' => $this->attachments,
        ];
    }

    public function getAdditionalData(): array
    {
        return [
            'conversation' => $this->getEmailConversation(),
        ];
    }

    /**
     * Get all replied-to emails in the conversation.
     *
     * @return array
     */
    public function getEmailConversation(): array
    {
        $conversation = [];
        $this->collectEmailThread($this, $conversation);

        return $conversation;
    }

    /**
     * Recursively collect the email thread in a conversation.
     *
     * @param EmailMessage $emailMessage
     * @param array &$conversation
     * @return void
     */
    private function collectEmailThread(EmailMessage $emailMessage, array &$conversation): void
    {
        $emailMessage->load('attachments');
        $conversation[] = $emailMessage;

        if ($emailMessage->replyToEmailMessage) {
            $this->collectEmailThread($emailMessage->replyToEmailMessage, $conversation);
        }
    }

    /**
     * Get the email this message is replying to.
     *
     * @return BelongsTo
     */
    public function replyToEmailMessage(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'reply_to_email_message_id', 'id');
    }

    /**
     * Get the user associated with this email.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        $this->keyType = 'string';

        return $this->hasMany(Attachment::class, 'related_id')
            ->where('related_name', 'email_messages');
    }
}
