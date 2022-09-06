<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\HashTag;

use DB;

class HashTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hashtags')->insert([
            "hashtag_id" => "9040a11a-63c8-4870-81e5-66628b307065",
            "hashtag_name" => 'Laravel',
        ]);
        DB::table('hashtags')->insert([
          "hashtag_id" => "9040a11a-63c8-4870-81e5-66628b307066",
          "hashtag_name" => 'Angular',
        ]);
        DB::table('hashtags')->insert([
          "hashtag_id" => "9040a11a-63c8-4870-81e5-66628b307067",
          "hashtag_name" => 'React',
        ]); 
    }
}
