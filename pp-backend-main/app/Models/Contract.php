<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'contract_id';
    protected $keyType = 'string';

    protected $fillable = [
        "contract_id",
        "contract_title",
        "contract_desc",
        "contract_max_hrs",
        "contract_hourly_rate",
        "contract_allow_manual_track",
        "contract_status",
        "channel_id",
    ];

    const CONTRACT_ST_PENDING = 'pending';
    const CONTRACT_ST_ACCEPTED = 'accepted';
    const CONTRACT_ST_CANCELED = 'canceled';
    const CONTRACT_ST_DECLINED = 'declined';
    const CONTRACT_ST_ENDED = 'ended';
}
