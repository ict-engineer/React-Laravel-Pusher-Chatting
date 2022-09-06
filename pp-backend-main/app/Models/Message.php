<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'msg_id';
    protected $keyType = 'string';

    protected $fillable = [
        "msg_id",
        "msg_body",
        "channel_id",
        "user_id",
        "is_tran",
    ];

    
}
