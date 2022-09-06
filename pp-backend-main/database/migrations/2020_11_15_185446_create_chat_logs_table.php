<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->string("chat_log_id")->unique();
            $table->string("channel_id");
            $table->string("user_id");
            $table->string("chat_type")->default("message");
            $table->string("chat_log_event_id");
            // $table->string("fre_id");
            // $table->string("clt_id");
            $table->integer("is_read")->default(0);
            // $table->tinyInteger("sender_type")->comment("Sender Type: 0: freelander, 1: client, 2: Support/Agent");
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
        Schema::dropIfExists('chat_logs');
    }
}
