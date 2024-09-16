<?php

namespace App\Services;

use App\Models\EmailMessage;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;

class EmailMessageService
{
    protected AttachmentService $attachmentService;
    protected EmailSettingService $emailSettingService;

    public function __construct()
    {
        $this->attachmentService = new AttachmentService();
        $this->emailSettingService = new EmailSettingService();
    }

    public function show(EmailMessage $emailMessage): EmailMessage
    {
        $emailMessage->update(['is_seen' => true]);

        return $emailMessage;
    }

    public function sendEmailUsingSmpt(): void
    {

    }

    public function getEmailsUsingIMAP(): Collection
    {
        return DB::transaction(function () {
            $imapConfig = $this->emailSettingService->setImapEmailConfig();

            $inboxMessages = $this->fetchEmailsFromFolder($imapConfig, 'INBOX', 30);
            $sentMessages = $this->fetchEmailsFromFolder($imapConfig, 'Išsiųsti laiškai', 30);

            $processedInboxEmails = $this->processEmails($inboxMessages, 'INBOX');
            $processedSentEmails = $this->processEmails($sentMessages, 'Išsiųsti laiškai');

            return $processedInboxEmails->merge($processedSentEmails);
        });
    }

    protected function fetchEmailsFromFolder(array $imapConfig, string $folderName, int $days): Collection
    {
        $client = Client::make($imapConfig);
        $client->connect();

        $folder = $client->getFolderByName($folderName);
        $messages = $folder?->query()?->since(now()->subDays($days))->get();

        return $messages ?? collect();
    }

    protected function processEmails(Collection $messages, string $folderName): Collection
    {
        $existingEmailMessages = EmailMessage::where('user_id', auth()->user()->id)->get();
        $existingEmailMessageIds = $existingEmailMessages->pluck('message_id');
        $createdEmailMessages = collect();

        foreach ($messages as $message) {
            $messageId = $message->getMessageId()->get()[0];

            if (!$existingEmailMessageIds->contains($messageId)) {
                $encodedSubject = $message->getSubject()->get()[0];
                $encodedSubject = imap_mime_header_decode($encodedSubject);

                $decodedSubject = '';
                foreach ($encodedSubject as $part) {
                    $decodedSubject .= $part->text;
                }
                $subject = $decodedSubject;

                $purifierConfig = HTMLPurifier_Config::createDefault();
                $purifier = new HTMLPurifier($purifierConfig);
                $bodyHtml = $purifier->purify($message->getHTMLBody());

                $fromEmails = collect($message->getFrom()?->get())->map(function ($fromEmailObject) {
                    return $fromEmailObject->mail;
                });

                $toEmails = collect($message->getTo()?->get())->map(function ($toEmailObject) {
                    return $toEmailObject->mail;
                });

                $ccEmails = collect($message->getCc()?->get())->map(function ($ccEmailObject) {
                    return $ccEmailObject->mail;
                });

                $bccEmails = collect($message->getBcc()?->get())->map(function ($bccEmailObject) {
                    return $bccEmailObject->mail;
                });

                $replyToEmails = collect($message->getReplyTo()?->get())->map(function ($replyToEmailObject) {
                    return $replyToEmailObject->mail;
                });

                $inReplyTo = $message->getInReplyTo()?->get()[0] ?? null;
                $inReplyTo = $inReplyTo ? trim($inReplyTo, '<>') : null;

                $replyToEmailMessage = null;
                if ($inReplyTo) {
                    $replyToEmailMessage = EmailMessage::where('message_id', $inReplyTo)->first();
                }

                $emailMessage = EmailMessage::create([
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'from' => $fromEmails->count() > 0 ? $fromEmails->join(',') : null,
                    'to' => $toEmails->count() > 0 ? $toEmails->join(',') : null,
                    'cc' => $ccEmails->count() > 0 ? $ccEmails->join(',') : null,
                    'bcc' => $bccEmails->count() > 0 ? $bccEmails->join(',') : null,
                    'reply_to' => $replyToEmails->count() > 0 ? $replyToEmails->join(',') : null,
                    'date' => $message->getDate()->get()[0]?->toDateTimeString() ?? null,
                    'body_text' => $message->getTextBody() ?? null,
                    'body_html' => $bodyHtml,
                    'is_seen' => $message->getFlags()->has('Seen') ?? false,
                    'is_flagged' => $message->getFlags()->has('Flagged') ?? false,
                    'is_answered' => $message->getFlags()->has('Answered') ?? false,
                    'folder' => $folderName,
                    'reply_to_email_message_id' => $replyToEmailMessage?->id,
                    'user_id' => auth()->id()
                ]);

                foreach ($message->getAttachments() as $attachment) {
//                    dd($attachment->name);
//                    dd($attachment->content);
//                    $this->attachmentService->store([
//                        'file' =>
//                        'related_name' => 'email_messages',
//                        'related_id' => $emailMessage->id,
//                    ]);

                    $this->attachmentService->store([
                        'name' => $attachment->name,        // File name
                        'content' => $attachment->content,  // File content
                        'related_name' => 'email_messages',
                        'related_id' => $emailMessage->id,
                    ]);
                }

                $createdEmailMessages->push($emailMessage);
            }
        }

        return $createdEmailMessages;
    }

}


