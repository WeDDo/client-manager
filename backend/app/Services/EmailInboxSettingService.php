<?php

namespace App\Services;

use App\Models\EmailInboxSetting;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Support\FolderCollection;

class EmailInboxSettingService
{
    public function store(array $data): EmailInboxSetting
    {
        if (strtoupper($data['name']) === 'INBOX' && EmailInboxSetting::where('name', $data['name'])->exists()) {
            throw new HttpResponseException(
                response()->json([
                    'error' => 'INBOX is already created!',
                ], 400)
            );
        }

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

    public function getInboxesImap(): array
    {
        if (!auth()->user()->activeImapEmailSetting()->first()) {
            return [[], []];
        }

        $imapConfig = (new EmailSettingService())->setImapEmailConfig();

        $client = Client::make($imapConfig);
        $client->connect();

        $folderNames = $this->extractMailboxNames($client->getFolders());

        $formattedFolders = [
            [],
            [],
        ];

        $emailInboxSettingNames = auth()->user()->emailInboxSettings()->pluck('name');

        foreach ($folderNames as $key => $folderName) {
            if (!$emailInboxSettingNames->contains($folderName)) {
                $formattedFolders[0][] = [
                    'id' => $key,
                    'name' => $folderName,
                ];
            }

        }

        return $formattedFolders;
    }

    protected function extractMailboxNames(FolderCollection $folders): array
    {
        $mailboxNames = [];

        foreach ($folders as $folder) {
            if ($folder->name && $folder->name !== '[Gmail]') {
                $mailboxNames[] = $folder->name;
            }

            if ($folder->hasChildren()) {
                $mailboxNames = array_merge($mailboxNames, $this->extractMailboxNames($folder->children));
            }
        }

        return $mailboxNames;
    }

    public function createInboxes(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data[1] as $inbox) {
                $emailSettingInbox = $this->store(['name' => $inbox['name']]);
            }
        });
    }
}


