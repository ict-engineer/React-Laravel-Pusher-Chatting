<?php

namespace Database\Seeders;

use App\Models\Freelancer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FreelancersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Freelancer::create([
            "fre_id" => "f082824b-796f-4f85-b9ea-881444671cce",
            "user_id" => "c7095959-1027-4ccf-b9ad-4fb2dd10d180",
            "fre_payment_email" => "freelancer1@gmail.com",
            "fre_full_name" => "",
            "fre_en_name" => "",
            "fre_phone" => "",
            "fre_skype_id" => "",
            "fre_avatar" => "",
            "fre_short_desc" => "",
            "fre_english_level_id" => "",
            "fre_rate" => "",
            "fre_timezone_id" => "",
            "fre_accept_offers" => "",
            "fre_show_en_name" => "",
            "fre_payment_verified" => ""
        ]);
    }
}
