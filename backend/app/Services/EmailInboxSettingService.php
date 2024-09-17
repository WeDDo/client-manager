<?php

namespace App\Services;

use App\Models\EmailInboxSetting;

class EmailInboxSettingService
{
    public function store(array $data): EmailInboxSetting
    {
        $data['user_id'] = auth()->user()->id;

        return EmailInboxSetting::create($data);
    }

    public function update(array $data, EmailInboxSetting $emailInboxSetting): void
    {
        $emailInboxSetting->update($data);
    }
}


