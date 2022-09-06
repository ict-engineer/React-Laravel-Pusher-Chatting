<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatType extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'chat_type_id';
    protected $keyType = 'string';

    protected $fillable = [
        "chat_type_id",
        "chat_type_name",
        "chat_type_action",
        "chat_type_desc"
    ];


    // chat type names
    const CHAT_NAME_MESSAGE = 'message';
    const CHAT_NAME_CONTRACT = 'contract';
    const CHAT_NAME_INVOICE = 'invoice';
    const CHAT_NAME_TIMETRACK = 'timetrack';
    const CHAT_NAME_REVIEW = 'review';

    // chat type actions
    const CHAT_ACTION_SENT = 'sent';
    const CHAT_ACTION_ACCEPTED = 'accepted';
    const CHAT_ACTION_EDITED = 'edited';
    const CHAT_ACTION_CANCELED = 'canceled';
    const CHAT_ACTION_DECLINED = 'declined';
    const CHAT_ACTION_ENDED = 'ended';
    const CHAT_ACTION_DELETED = 'deleted';


}
