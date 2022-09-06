<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'channel_id';
    protected $keyType = 'string';

    protected $fillable = [
        "channel_id",
        "portfolio_id",
        "contract_id",
        "last_time",
        "channel_status",
        "fre_id",
        "clt_id",
        "job_id",
    ];

    
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class, 'fre_id');
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'clt_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
    
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id');
    }
}
