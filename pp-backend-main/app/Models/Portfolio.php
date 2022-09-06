<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'por_id';
    protected $keyType = 'string';

    protected $fillable = [
        'por_id',
        'fre_id',
        'por_title',
        'por_desc',
        'por_platform_verified',
        'por_done_inside_platform',
        'por_helped',
        'por_viewed',
        'por_status'
    ];

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class, 'fre_id');
    }

    public function portfolio_images()
    {
        return $this->hasMany(PortfolioImage::class, "por_id", "por_id");
    }

    public function portfolio_hashtags()
    {
        return $this->hasMany(PortfolioHashTag::class, "por_id", "por_id");
    }
}
