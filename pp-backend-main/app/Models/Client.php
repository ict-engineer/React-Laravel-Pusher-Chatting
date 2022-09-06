<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'clt_id';
    protected $keyType = 'string';

    protected $fillable = [
        "clt_id",
        "user_id",
        "clt_invoice_email",
        "clt_full_name",
        "clt_phone",
        "clt_skype_id",
        "clt_avatar",
        "clt_payment_verified"
    ];

}
