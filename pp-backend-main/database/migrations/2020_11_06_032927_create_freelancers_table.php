<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freelancers', function (Blueprint $table) {
            $table->string("fre_id")->unique();
            $table->string("user_id");
            $table->string("fre_full_name");
            $table->string("fre_first_name");
            $table->string("fre_last_name");
            $table->string("fre_timezone");
            $table->string("fre_phone")->nullable();
            $table->string("fre_en_name")->nullable();
            $table->string("fre_skype_id")->nullable();
            $table->string("fre_avatar")->nullable();
            $table->text("fre_desc")->nullable();
            $table->string("fre_english_level")->nullable();
            $table->float("fre_rate")->default(0);
            $table->boolean("fre_accept_offers")->default(1);
            $table->boolean("fre_show_en_name")->default(1);
            $table->integer("fre_rate_req_status")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freelancers');
    }
}
