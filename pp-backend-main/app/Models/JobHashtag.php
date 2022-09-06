<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobHashtag extends Model
{
    use HasFactory;
    
    protected $table = 'job_hashtags';
    public $incrementing = false;
    protected $primaryKey = 'job_hashtag_id';
    protected $keyType = 'string';

    protected $fillable = [
        'job_hashtag_id',
        'job_id',
        'hashtag_id'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'por_id');
    }

    public function hashtag()
    {
        return $this->belongsTo(HashTag::class, 'hashtag_id');
    }
}
