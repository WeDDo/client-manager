<?php

namespace App\Services;

use App\Models\EmailInboxSetting;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmailInboxSettingService
{
    public function store(array $data): EmailInboxSetting
    {
        if (strtoupper($data['name']) === 'INBOX') {
            throw new HttpResponseException(
                response()->json([
                    'error' => 'INBOX is already created!',
                ], 400)
            );
        }

        $data['user_id'] = auth()->user()->id;

        return EmailInboxSetting::create($data);
    }

    public function update(array $data, EmailInboxSetting $emailInboxSetting): void
    {
        if (strtoupper($emailInboxSetting->name) === 'INBOX' && strtoupper($data['name']) !== 'INBOX') {
            throw new HttpResponseException(
                response()->json([
                    'error' => 'This inbox name cannot be changed!',
                ], 400)
            );
        }

        $emailInboxSetting->update($data);
    }

    public function destroy(EmailInboxSetting $emailInboxSetting): void
    {
        if (strtoupper($emailInboxSetting->name) === 'INBOX') {
            throw new HttpResponseException(
                response()->json([
                    'error' => 'The INBOX setting cannot be deleted!',
                ], 400)
            );
        }

        $emailInboxSetting->delete();
    }
}


