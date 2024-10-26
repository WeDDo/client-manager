<?php

namespace App\Models;

use App\Traits\CreateUpdateUserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use HasFactory, CreateUpdateUserTrait;

    protected $fillable = [
        'name',
        'is_private',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
