<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'review_id';
    protected $keyType = 'string';

    protected $fillable = [
        'review_id',
        'contract_id',
        'review_rating',
        'review_feedback',
        'author_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

}