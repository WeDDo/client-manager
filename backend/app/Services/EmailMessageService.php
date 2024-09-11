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
    protected EmailSettingService $emailSettingService;

    public function __construct()
    {
        $this->emailSettingService = new EmailSettingService();
    }

    public function getEmailsUsingIMAP(): Collection
    {
        return DB::transaction(function () {
            $imapConfig = $this->emailSettingService->setImapEmailConfig();

            $client = Client::make($imapConfig);
            $client->connect();

            $folder = $client->getFolder('INBOX');
            $messages = $folder->query()->since(now()->subDays(5))->get();

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

//                    $referenceMessageIds = explode(' ', $message->getReferences()->get()[0]);
//                    $referenceMessageIds = collect($referenceMessageIds)->map(function ($reference) {
//                        return trim($reference, '<>');
//                    });

                    $inReplyTo = $message->getInReplyTo()?->get()[0] ?? null;
                    $inReplyTo = $inReplyTo ? trim($inReplyTo, '<>') : null;

                    $replyToEmailMessage = null;
                    if ($inReplyTo) {
                        $replyToEmailMessage = EmailMessage::where('message_id', $inReplyTo)->first();
                    }

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
                        'folder' => $folder->name ?? null,
                        'reply_to_email_message_id' => $replyToEmailMessage?->id,
                        'user_id' => auth()->id()
                    ]);

                    $createdEmailMessages->push($emailMessage);
                }
            }

            return $createdEmailMessages;
        });
    }
}


