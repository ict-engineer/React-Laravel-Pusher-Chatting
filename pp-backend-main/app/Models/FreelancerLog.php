<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "fre_log_id",
        "fre_id",
        "fre_log_field_name",
        "fre_log_new_value"
    ];

}
