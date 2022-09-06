<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'inv_dtl_id';
    protected $keyType = 'string';

    protected $fillable = [
        "inv_dtl_id",
        "inv_id",
        "inv_dtl_is_manual",
        "inv_dtl_date",
        "inv_dtl_from",
        "inv_dtl_to",
        "inv_dtl_total_hrs",
        "inv_dtl_hourly_rate",
    ];
}
