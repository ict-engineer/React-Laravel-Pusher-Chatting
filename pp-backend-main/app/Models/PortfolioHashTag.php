<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioHashTag extends Model
{
    use HasFactory;

    protected $table = 'portfolio_hashtags';
    public $incrementing = false;
    protected $primaryKey = 'por_hashtag_id';
    protected $keyType = 'string';

    protected $fillable = [
        'por_hashtag_id',
        'por_id',
        'hashtag_id'
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'por_id');
    }

    public function hashtag()
    {
        return $this->belongsTo(HashTag::class, 'hashtag_id');
    }
}
