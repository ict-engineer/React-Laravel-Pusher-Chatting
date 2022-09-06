<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'inv_id';
    protected $keyType = 'string';

    protected $fillable = [
        "inv_id",
        "contract_id",
        "inv_sub_total",
        "inv_fee",
        "inv_total",
        "inv_type",
        "inv_status",
        "por_id"
    ];
}
