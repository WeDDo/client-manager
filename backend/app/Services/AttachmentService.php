<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    public function store(array $data): Attachment
    {
        $file = $data['file'] ?? null;
        $fileName = $data['name'] ?? null;
        $fileContent = $data['content'] ?? null;
        $relatedName = $data['related_name'];
        $relatedId = $data['related_id'];

        $storagePathPrefix = $relatedName;
        if ($relatedId) {
            $storagePathPrefix .= "/$relatedId";
        }

        if ($file) {
            $filename = $file->getClientOriginalName();
            $storageFilename = time() . "_$filename";

            $filePath = $file->storeAs("attachments/$storagePathPrefix", $storageFilename, 'local');
        } elseif ($fileContent && $fileName) {
            $storageFilename = time() . "_$fileName";
            $filePath = "attachments/$storagePathPrefix/$storageFilename";

            Storage::put($filePath, $fileContent);
        } else {
            throw new \Exception('No file or content provided.');
        }

        return Attachment::create([
            'related_name' => $relatedName,
            'related_id' => $relatedId,
            'storage_url' => Storage::url($filePath),
            'filename' => $filename ?? $fileName,
        ]);
    }
}


