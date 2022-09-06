<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerTimezone extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'fre_timezone_id';
    protected $keyType = 'string';

    protected $table = "freelancer_timezone";
    public $timestamps = false;

    protected $fillable = [
        "fre_timezone_id",
        "fre_id",
        "timezone_id"
    ];

    public function timezone()
    {
        return $this->hasOne(Timezone::class, 'timezone_id', 'timezone_id');
    }

}
