<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_tracks', function (Blueprint $table) {
            $table->string("trk_id")->unique();
            $table->string("contract_id");
            $table->boolean("trk_is_manual");
            $table->date("trk_date");
            $table->time("trk_from");
            $table->time("trk_to");
            $table->float('trk_total_hrs',10,2)->nullable();
            $table->boolean("trk_status");
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
        Schema::dropIfExists('time_tracks');
    }
}
