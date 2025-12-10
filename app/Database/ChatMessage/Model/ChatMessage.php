<?php

namespace App\Database\ChatMessage\Model;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_id',
        'role',
        'content',
    ];
}
