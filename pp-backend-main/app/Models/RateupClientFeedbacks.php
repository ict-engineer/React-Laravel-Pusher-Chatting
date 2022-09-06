<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateupClientFeedbacks extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $fillable = [
        "id",
        "fre_id",
        "clt_id",
        "current_rate",
        "able_rate",
        "rateup",
        "hire_able",
        "fre_name",
        "clt_name",
        "project_name"
    ];
}
