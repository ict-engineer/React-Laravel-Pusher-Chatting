<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTrack extends Model
{
    use HasFactory;
    protected $table = 'time_tracks';
    public $incrementing = false;
    protected $primaryKey = 'trk_id';
    protected $keyType = 'string';

    protected $fillable = [
        "trk_id",
        "contract_id",
        "trk_is_manual",
        "trk_date",
        "trk_from",
        "trk_to",
        "trk_total_hrs",
        "trk_status",
        "created_at",
        "updated_at"
    ];
}
