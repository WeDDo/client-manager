<?php

namespace App\Services;

use App\Models\EmailMessage;
use Carbon\Carbon;
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
            $messages = $folder->query()->since(now()->subDays(30))->get();

            $existingEmailMessages = EmailMessage::where('user_id', auth()->user()->id)->get();
            $createdEmailMessages = collect();
            foreach ($messages as $message) {
                $encodedSubject = $message->getSubject()->get()[0];
                $encodedSubject = imap_mime_header_decode($encodedSubject);

                $decodedSubject = '';

                foreach ($encodedSubject as $part) {
                    $decodedSubject .= $part->text;
                }
                $subject = $decodedSubject;
                $messageId = $message->getMessageId()->get()[0];

                $purifierConfig = HTMLPurifier_Config::createDefault();
                $purifier = new HTMLPurifier($purifierConfig);
                $bodyHtml = $purifier->purify($message->getHTMLBody());

//                if ($existingEmailMessages->pluck('message_id')->contains($messageId)) {
//                    $existingEmailMessage = $existingEmailMessages->where('message_id', $messageId)->first();
////                    if ($existingEmailMessage->body_html !== $bodyHtml) {
////                        dd($bodyHtml);
//                        $existingEmailMessage->update([
//                            'subject' => $subject,
//                            'date' => $message->getDate()->get()[0]?->toDateTimeString() ?? null,
//                            'body_text' => $message->getTextBody() ?? null,
//                            'body_html' => $bodyHtml,
//                        ]);
////                    }
//                } else {
                    $emailMessage = EmailMessage::create([
                        'message_id' => $messageId,
                        'subject' => $subject,
                        'from' => $message->getFrom()[0]->mail ?? null,
                        'to' => $message->getTo()?->get()[0]?->mail ?? null,
                        'cc' => $message->getCc()?->get()[0]?->mail ?? null,
                        'bcc' => $message->getBcc()?->get()[0]?->mail ?? null,
                        'reply_to' => $message->getReplyTo()[0]?->mail ?? null,
                        'date' => $message->getDate()->get()[0]?->toDateTimeString() ?? null,
                        'body_text' => $message->getTextBody() ?? null,
                        'body_html' => $bodyHtml,
                        'is_seen' => $message->getFlags()->has('Seen') ?? false,
                        'is_flagged' => $message->getFlags()->has('Flagged') ?? false,
                        'is_answered' => $message->getFlags()->has('Answered') ?? false,
                        'folder' => $folder->name ?? null,
                        'user_id' => auth()->id()
                    ]);

                    $createdEmailMessages->push($emailMessage);
//                }
            }

            return $createdEmailMessages;
        });
    }
}


