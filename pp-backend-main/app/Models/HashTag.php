<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HashTag extends Model
{
    use HasFactory;

    protected $table = 'hashtags';
    public $incrementing = false;
    protected $primaryKey = 'hashtag_id';
    protected $keyType = 'string';

    
    protected $fillable = [
        "hashtag_id",
        "hashtag_name"
    ];

    public function portfolios()
    {
        return $this->belongsToMany(Portfolio::class, 'portfolio_hashtags', 'portfolio_id', 'hashtag_id');
    }

}
