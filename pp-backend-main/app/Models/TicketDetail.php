<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'ticket_details';
    protected $keyType = 'string';
    protected $primaryKey = 'ticket_dtl_id';

    protected $fillable = [
        'ticket_dtl_id',
        'ticket_id',
        'user_id',
        'ticket_dtl_msg',
        'is_read',
    ];
}
