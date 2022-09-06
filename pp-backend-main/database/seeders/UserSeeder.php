<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            "user_id" => "9040a11a-63c8-4870-81e5-66628b307065",
            "user_email" => 'freelancer@gmail.com',
            "user_role" => "freelancer",
            "user_password" => Hash::make('password'),
            "user_access_token" => "",
            "user_social_provider" => ""
        ]);
        DB::table('users')->insert([
            "user_id" => "c7095959-1027-4ccf-b9ad-4fb2dd10d180",
            "user_email" => 'freelancer1@gmail.com',
            "user_role" => "freelancer",
            "user_password" => Hash::make('password'),
            "user_access_token" => "",
            "user_social_provider" => ""
        ]);
    }
}
