<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;
    protected $primaryKey = null;

    public $incrementing = false;
    protected $fillable = [
        "payout_id",
        "inv_id",
        "status",
        "amount",
        "currency",
    ];
}
