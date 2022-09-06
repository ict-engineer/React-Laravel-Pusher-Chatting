<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "contract_log_id",
        "contract_id",
        "contract_log_field_name",
        "contract_log_new_value"
    ];

}
