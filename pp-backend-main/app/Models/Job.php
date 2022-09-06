<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs';
    public $incrementing = false;
    protected $primaryKey = 'job_id';
    protected $keyType = 'string';

    protected $fillable = [
        "job_id",
        "job_title",
        "job_desc",
        "job_status",
        "user_id",
    ];

    public function user(){
      $this->belongsTo(User::class, 'user_id');
    }
}
