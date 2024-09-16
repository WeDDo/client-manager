<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'related_name',
        'related_id',
        'storage_url',
        'filename',
    ];

    public function getStorageUrlAttribute($value): ?string
    {
        if(!$value) return null;

        return str_replace('/storage/', '', $value);
    }

    public function getBase64(): string
    {
        $file = Storage::get($this->storage_url);
        $type = Storage::mimeType($this->filename);

        $base64 = base64_encode($file);

        return "data:$type;base64,$base64";
    }
}
