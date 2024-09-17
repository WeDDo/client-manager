<?php

namespace App\Services;

use App\Models\EmailSetting;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class EmailSettingService
{
    public function show(EmailSetting $emailSetting): EmailSetting
    {
        $emailSetting->password = Crypt::decryptString($emailSetting->password);

        return $emailSetting;
    }

    public function store(array $data): EmailSetting
    {
        $data['password'] = Crypt::encryptString($data['password']);

        $emailSetting = auth()->user()->emailSettings()->create($data);

        $this->checkActiveEmailSettings($data, $emailSetting);

        return $emailSetting;
    }

    public function update(array $data, EmailSetting $emailSetting): void
    {
        if (isset($data['password'])) {
            $data['password'] = Crypt::encryptString($data['password']);
        }

        $emailSetting->update($data);

        $this->checkActiveEmailSettings($data, $emailSetting);
    }

    public function checkActiveEmailSettings(array $data, EmailSetting $emailSetting): void
    {
        if (isset($data['active']) && $data['active']) {
            auth()->user()->emailSettings()
                ->where('id', '<>', $emailSetting->id)
                ->update(['active' => false]);
        }
    }
//    public function getEmailsFromInbox()
//    {
//
//    }

    public function copy(EmailSetting $emailSetting): EmailSetting
    {
        $newEmailSetting = $emailSetting->replicate();
        $newEmailSetting->active = false;
        $newEmailSetting->save();
        return $newEmailSetting;
    }

    public function checkConnection(EmailSetting $emailSetting): void
    {
        $client = Client::make($this->setImapEmailConfig($emailSetting));
        try {
            $client->connect();
        } catch (ConnectionFailedException $e) {
            throw new HttpResponseException(
                response()->json([
                    'error' => 'connection_failed'
                ], 401)
            );
        }
    }

    public function setImapEmailConfig(?EmailSetting $emailSetting = null): array
    {
        $user = auth()->user();
        if (!$emailSetting) {
            $emailSetting = $user->emailSettings()
                ->where('protocol', EmailSetting::$imapProtocol)
                ->where('active', true)
                ->first();
        }

        config([
            "imap.users.$user->id" => [
                'host' => $emailSetting->host,
                'port' => $emailSetting->port,
                'encryption' => $emailSetting->encryption,
                'validate_cert' => $emailSetting->validate_cert,
                'username' => $emailSetting->username,
                'password' => Crypt::decryptString($emailSetting->password),
                'protocol' => $emailSetting->protocol,
            ]
        ]);

        return config("imap.users.$user->id");
    }

    public function setSmtpEmailConfig(?EmailSetting $emailSetting = null): array
    {
        $user = auth()->user();
        if (!$emailSetting) {
            $emailSetting = $user->emailSettings()
                ->where('protocol', EmailSetting::$smtpProtocol)
                ->where('active', true)
                ->first();
        }

        $config = [
            'transport' => 'smtp',
            'host' => $emailSetting->host,
            'port' => $emailSetting->port,
            'encryption' => $emailSetting->encryption,
            'username' => $emailSetting->username,
            'password' => Crypt::decryptString($emailSetting->password),
            'auth_mode' => 'LOGIN',
            'authentication' => 'LOGIN',
            'timeout' => null,
            'validate_cert' => $emailSetting->validate_cert ?? true,
        ];

        config(["mail.mailers.smtp.users.$user->id" => $config]);

        return config("mail.mailers.smtp.users.$user->id");
    }
}


