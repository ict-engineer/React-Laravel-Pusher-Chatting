<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnglishLevel extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'english_level_id';
    protected $keyType = 'string';

    protected $fillable = [
        "english_level_id",
        "english_level_title",
        "english_level_desc"
    ];

}
