<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HashTag;

class HashTagController extends Controller
{
    //
    public function index()
    {
      $hashtags = HashTag::pluck('hashtag_name');
      return response()->json(['message' => 'Get hashtags successfully.', 'data' => $hashtags]);
    }
}
