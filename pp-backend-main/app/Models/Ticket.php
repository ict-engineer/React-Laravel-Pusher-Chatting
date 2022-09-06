<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'tickets';
    protected $keyType = 'string';
    protected $primaryKey = 'ticket_id';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'ticket_status',
        'ticket_title',
        'ticket_description',
    ];
}
