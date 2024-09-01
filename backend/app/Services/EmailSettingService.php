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
            auth()->user()->emailSettings()->where('id', '<>', $emailSetting->id)->update(['active' => false]);
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


