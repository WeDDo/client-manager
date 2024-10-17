<?php

namespace App\Mail;

use App\Models\EmailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class Email extends Mailable// implements ShouldQueue
{
    use Queueable, SerializesModels;

    private ?array $data;
    private ?EmailMessage $emailMessage;
    private ?string $emailSubject;
    public ?array $files;

    public function __construct(
        ?array        $data = null,
        ?EmailMessage $emailMessage = null,
        ?string       $emailSubject = null,
        ?array        $files = [],
    )
    {
        $this->data = $data;
        $this->emailMessage = $emailMessage;
        $this->emailSubject = $emailSubject;
        $this->files = $files;

        $this->emailMessage?->update(['is_answered' => true]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailMessage?->subject ?? $this->emailSubject ?? 'Email message',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.emailMessages.emailMessage',
            with: [
                'data' => $this->data,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $attachments[] = Attachment::fromPath($file->getRealPath())
                    ->as($file->getClientOriginalName())
                    ->withMime($file->getMimeType());
            }
        }

        return $attachments;
    }

    public function headers(): Headers
    {
        if ($this->emailMessage) {
            $references = $this->buildReferences($this->emailMessage);
            $inReplyTo = $this->emailMessage->message_id;

            return new Headers(
                references: $references,
                text: [
                    'In-Reply-To' => $inReplyTo ? "<$inReplyTo>" : null,
                ],
            );
        }

        return new Headers();
    }

    protected function buildReferences(?EmailMessage $emailMessage = null): array
    {
        $references = [];
        if ($emailMessage) {
            $currentEmail = $emailMessage;

            while ($currentEmail) {
                $references[] = "$currentEmail->message_id";
                $currentEmail = $currentEmail->replyToEmailMessage;
            }
        }

        return array_reverse($references);
    }
}

