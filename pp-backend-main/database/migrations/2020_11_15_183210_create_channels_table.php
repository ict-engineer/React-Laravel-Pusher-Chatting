<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
          $table->string("channel_id")->unique();
            $table->string("portfolio_id")->nullable();
            $table->string("contract_id")->nullable();
            $table->timestamp('last_time')->useCurrent();
            $table->boolean("channel_status")->default(true);
            $table->string("fre_id");
            $table->string("clt_id");
            $table->string("job_id");
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
        Schema::dropIfExists('channels');
    }
}
