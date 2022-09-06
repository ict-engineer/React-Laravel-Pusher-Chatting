<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        "dispute_id",
        "contract_id",
        "user_id",
        "dispute_status"
    ];
    
}
