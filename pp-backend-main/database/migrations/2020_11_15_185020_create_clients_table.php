<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string("clt_id")->unique();
            $table->string("user_id");
            $table->string("clt_invoice_email")->nullable();
            $table->string("clt_full_name")->nullable();
            $table->string("clt_phone")->nullable();
            $table->string("clt_skype_id")->nullable();
            $table->string("clt_avatar")->nullable();
            $table->boolean("clt_payment_verified")->nullable();
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
        Schema::dropIfExists('clients');
    }
}
