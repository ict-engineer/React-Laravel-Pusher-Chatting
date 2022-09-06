<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->string("contract_id")->unique();
            $table->string("contract_title");
            $table->string("contract_desc");
            $table->integer("contract_max_hrs");
            $table->integer("contract_hourly_rate");
            $table->boolean("contract_allow_manual_track");
            $table->string("contract_status");
            $table->string("channel_id");
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
        Schema::dropIfExists('contracts');
    }
}
