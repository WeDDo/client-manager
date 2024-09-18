<?php

namespace App\Services;

use App\Mail\Email;
use App\Models\EmailInboxSetting;
use App\Models\EmailMessage;
use App\Models\EmailSetting;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

    public function getAdditionalData(): array
    {
        return [
            'email_inbox_settings' => EmailInboxSetting::where('user_id', auth()->user()->id)->pluck('name'),
        ];
    }

//    public function sendEmailUsingSMTP(
//        array $toEmails = [],
//        array $ccEmails = [],
//        array $bccEmails = [],
//    ): void
//    {
//        $emailSetting = auth()->user()->emailSettings()
//            ->where('protocol', EmailSetting::$smtpProtocol)
//            ->where('active', true)
//            ->first();
//
//        $this->emailSettingService->setSmtpEmailConfig($emailSetting);
//
//        DB::transaction(function () use ($toEmails, $ccEmails, $bccEmails) {
//            Mail::mailer('smtp.users.'. auth()->user()->id)
//                ->to($toEmails)
//                ->cc($ccEmails)
//                ->cc($bccEmails)
//                ->send(new Email());
//        });
//    }

//
    public function sendEmailUsingSMTP(
        array         $toEmails = [],
        array         $ccEmails = [],
        array         $bccEmails = [],
        ?string       $replyHtml = null,
        ?EmailMessage $emailMessage = null,
        ?string       $subject = null,
    ): void
    {
        $emailSetting = auth()->user()->emailSettings()
            ->where('protocol', EmailSetting::$smtpProtocol)
            ->where('active', true)
            ->first();

        $this->emailSettingService->setSmtpEmailConfig($emailSetting);

        $email = new Email([
            'text' => $replyHtml
        ], $emailMessage);

//        $email->buildHeaders();

        DB::transaction(function () use ($email, $toEmails, $ccEmails, $bccEmails, $emailMessage, $replyHtml, $subject) {
//            if ($emailMessage) {
//                $references = $this->buildReferences($emailMessage);
//
//                $email->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) use ($references, $emailMessage) {
//                    $message->subject($emailMessage->subject);
//                    $message->getHeaders()->addTextHeader('In-Reply-To', "<{$emailMessage->message_id}>");
//
//                    if ($references) {
//                        $message->getHeaders()->addTextHeader('References', $references);
//                    }
//                });
//            } else {
//                $email->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) use ($subject) {
//                    $message->subject($subject);
//                });
//            }

            Mail::mailer('smtp.users.' . auth()->user()->id)
                ->to($toEmails)
                ->cc($ccEmails)
                ->bcc($bccEmails)
                ->send($email);
        });
    }

//    protected function buildReferences(EmailMessage $emailMessage): ?string
//    {
//        $references = [];
//        $references[] = "<$emailMessage->message_id>";
//
//        return !empty($references) ? implode(' ', array_reverse($references)) : null;
//    }

    public function getEmailsUsingIMAP(): Collection
    {
        return DB::transaction(function () {
            $imapConfig = $this->emailSettingService->setImapEmailConfig();

            $emailInboxSettings = EmailInboxSetting::where('user_id', auth()->id())->get();
            $allProcessedEmails = collect();

            foreach ($emailInboxSettings as $inboxSetting) {
                $inboxMessages = $this->fetchEmailsFromFolder($imapConfig, $inboxSetting->name, 30);
                $processedInboxEmails = $this->processEmails($inboxMessages, $inboxSetting->name);
                $allProcessedEmails = $allProcessedEmails->merge($processedInboxEmails);
            }

            return $allProcessedEmails;
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
                    $this->attachmentService->store([
                        'name' => $attachment->name,
                        'content' => $attachment->content,
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


