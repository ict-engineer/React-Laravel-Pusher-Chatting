<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    use HasFactory;

    protected $table = 'portfolio_images';
    public $incrementing = false;
    protected $primaryKey = 'por_image_id';
    protected $keyType = 'string';

    protected $fillable = [
        'por_image_id',
        'por_id',
        'por_image_url'
    ];
}