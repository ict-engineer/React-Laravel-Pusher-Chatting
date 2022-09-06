<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "clt_log_id",
        "clt_id",
        "clt_log_field_name",
        "clt_log_new_value"
    ];

}
