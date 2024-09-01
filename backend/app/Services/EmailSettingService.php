<?php

namespace App\Services;

use App\Models\EmailSetting;
use Illuminate\Support\Facades\Crypt;

class EmailSettingService
{
    public function show(EmailSetting $emailSetting): EmailSetting
    {
        $emailSetting->password = Crypt::decryptString($emailSetting->password);

        return $emailSetting;
    }

    public function store(array $data): EmailSetting
    {
        // Encrypt the password before storing it
        $data['password'] = Crypt::encryptString($data['password']);

        return auth()->user()->emailSettings()->create($data);
    }

    public function update(array $data, EmailSetting $emailSetting): void
    {
        // Encrypt the password before updating it
        if (isset($data['password'])) {
            $data['password'] = Crypt::encryptString($data['password']);
        }

        $emailSetting->update($data);
    }
//    public function getEmailsFromInbox()
//    {
//
//    }

    public function copy(EmailSetting $emailSetting): EmailSetting
    {
        $newEmailSetting = $emailSetting->replicate();
        $newEmailSetting->save();
        return $newEmailSetting;
    }

    public function setImapEmailConfig()
    {
        $user = auth()->user();
        $emailSetting = $user->emailSettings()->first();

        config([
            "imap.users.$user->id" => [
                'host' => $emailSetting->host,
                'port' => $emailSetting->port,
                'encryption' => $emailSetting->encryption,
                'validate_cert' => $emailSetting->validate_cert,
                'username' => $emailSetting->username,
                'password' => $emailSetting->password,
                'protocol' => $emailSetting->protocol,
            ]
        ]);

        return config("imap.users.$user->id");
    }
}


