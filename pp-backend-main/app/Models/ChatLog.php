<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'chat_log_id';
    protected $keyType = 'string';

    protected $fillable = [
        "chat_log_id",
        "channel_id",
        "user_id",
        "chat_type",
        "chat_log_event_id",
        // "fre_id",
        // "clt_id",
        // "sender_type",
        "is_read"
    ];

    const CHATLOG_SENDER_CLIENT = 0;
    const CHATLOG_SENDER_FREELANCER = 1;
    /*
      sender_type : 0=> client, 1=> freelancer  

     */
}
