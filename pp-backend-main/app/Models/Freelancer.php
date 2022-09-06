<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'fre_id';
    protected $keyType = 'string';

    protected $fillable = [
        'fre_id',
        'user_id',
        'fre_payment_email',
        'fre_full_name',
        'fre_first_name',
        'fre_last_name',
        'fre_phone',
        'fre_skype_id',
        'fre_avatar',
        'fre_description',
        'fre_english_level',
        'fre_rate',
        'fre_timezone_id',
        'fre_accept_offers',
        'fre_payment_verified',
        'fre_rate_req_status',
        'fre_en_name',
        'fre_show_en_name',
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function freelancer_timezone()
    {
        return $this->hasMany(FreelancerTimezone::class);
    }
}
