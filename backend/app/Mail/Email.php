<?php

namespace App\Mail;

use App\Models\EmailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class Email extends Mailable //implements ShouldQueue
{
    use Queueable, SerializesModels;

    private ?array $data;
    private ?EmailMessage $emailMessage;
    /**
     * Create a new message instance.
     */
    public function __construct(?array $data = null, ?EmailMessage $emailMessage = null)
    {
        $this->data = $data;
        $this->emailMessage = $emailMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email message',
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
        return [];
    }

    public function headers(): Headers
    {
        return new Headers(
            messageId: $this->emailMessage->message_id,
            references: ["<{$this->emailMessage->message_id}>"],
        );
    }
}

